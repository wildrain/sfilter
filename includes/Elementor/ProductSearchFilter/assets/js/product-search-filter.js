(function($) {
    'use strict';

    function SFilterProductSearch($wrapper) {
        this.$wrapper    = $wrapper;
        this.$results    = $wrapper.find('.msf-results');
        this.$pagination = $wrapper.find('.msf-pagination-container');
        this.$overlay    = $wrapper.find('.msf-loading-overlay');

        this.ajaxUrl  = $wrapper.data('ajax-url');
        this.nonce    = $wrapper.data('nonce');
        this.settings = $wrapper.data('settings') || {};

        this.currentPage       = 1;
        this.currentView       = this.settings.default_view || 'grid';
        this.currentSort       = this.settings.default_sort || 'date';
        this.currentSearchType = '';
        this.currentSearch     = '';
        this.xhr               = null;
        this.searchTimer       = null;

        this.init();
    }

    SFilterProductSearch.prototype = {

        init: function() {
            this.bindEvents();
            this.initPriceRange();

            if (this.settings.enable_url_sync) {
                this.restoreFromUrl();
            }

            if (this.settings.pagination_type === 'infinite_scroll') {
                this.initInfiniteScroll();
            }
        },

        bindEvents: function() {
            var self = this;

            // Search input (debounced)
            this.$wrapper.on('input', '.msf-search-input', function() {
                self.currentSearchType = '';
                self.currentSearch = $(this).val() || '';
                clearTimeout(self.searchTimer);
                self.searchTimer = setTimeout(function() {
                    self.currentPage = 1;
                    self.fetchProducts(false);
                }, 300);
            });

            // Taxonomy select
            this.$wrapper.on('change', 'select.msf-taxonomy-filter', function() {
                self.currentPage = 1;
                self.fetchProducts(false);
            });

            // Taxonomy radio
            this.$wrapper.on('change', 'input[type="radio"].msf-taxonomy-filter', function() {
                self.currentPage = 1;
                self.fetchProducts(false);
            });

            // Taxonomy checkbox
            this.$wrapper.on('change', 'input[type="checkbox"].msf-taxonomy-filter', function() {
                self.currentPage = 1;
                self.fetchProducts(false);
            });

            // Price range slider
            this.$wrapper.on('input', '.msf-price-range-min, .msf-price-range-max', function() {
                self.updatePriceRange();
            });

            this.$wrapper.on('change', '.msf-price-range-min, .msf-price-range-max', function() {
                self.currentPage = 1;
                self.fetchProducts(false);
            });

            // Sort
            this.$wrapper.on('change', '.msf-sort-select', function() {
                self.currentSort = $(this).val();
                self.currentPage = 1;
                self.fetchProducts(false);
            });

            // View toggle
            this.$wrapper.on('click', '.msf-view-btn', function() {
                var view = $(this).data('view');
                if (view === self.currentView) return;

                self.$wrapper.find('.msf-view-btn').removeClass('msf-view-btn--active');
                $(this).addClass('msf-view-btn--active');
                self.currentView = view;
                self.currentPage = 1;
                self.fetchProducts(false);
            });

            // Numbered pagination
            this.$wrapper.on('click', '.msf-pagination__page', function() {
                self.currentPage = parseInt($(this).data('page'), 10);
                self.fetchProducts(false);
                self.scrollToTop();
            });

            // Load more
            this.$wrapper.on('click', '.msf-pagination__load-more', function() {
                self.currentPage = parseInt($(this).data('page'), 10);
                self.fetchProducts(true);
            });

            // Reset button
            this.$wrapper.on('click', '.msf-reset-btn', function() {
                self.$wrapper.find('.msf-search-input').val('');
                self.$wrapper.find('select.msf-taxonomy-filter').val('');
                self.$wrapper.find('input[type="radio"].msf-taxonomy-filter[value=""]').prop('checked', true);
                self.$wrapper.find('input[type="checkbox"].msf-taxonomy-filter').prop('checked', false);

                // Reset price range slider
                var $rangeWrapper = self.$wrapper.find('.msf-price-range-wrapper');
                if ($rangeWrapper.length) {
                    var rangeMin = parseInt($rangeWrapper.data('min'), 10) || 0;
                    var rangeMax = parseInt($rangeWrapper.data('max'), 10) || 1000;
                    self.$wrapper.find('.msf-price-range-min').val(rangeMin);
                    self.$wrapper.find('.msf-price-range-max').val(rangeMax);
                    self.updatePriceRange();
                }

                self.$wrapper.find('.msf-term-item--expanded').removeClass('msf-term-item--expanded');
                self.$wrapper.find('.msf-term-toggle').text('+');
                self.currentSearchType = '';
                self.currentSearch = '';
                $('.sf-search-wrapper .sf-search-input').val('');
                $('.sf-search-wrapper .sf-search-textarea').val('');
                self.currentSort = self.settings.default_sort || 'date';
                self.$wrapper.find('.msf-sort-select').val(self.currentSort);
                self.currentPage = 1;
                if (self.settings.enable_url_sync) {
                    history.pushState(null, '', window.location.pathname);
                }
                self.fetchProducts(false);
            });

            // Term tree toggle (expand/collapse children)
            this.$wrapper.on('click', '.msf-term-toggle', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $item = $(this).closest('.msf-term-item');
                $item.toggleClass('msf-term-item--expanded');
                $(this).text($item.hasClass('msf-term-item--expanded') ? '−' : '+');
            });

            // Quantity minus
            this.$wrapper.on('click', '.msf-qty-minus', function() {
                var $input = $(this).siblings('.msf-qty-input');
                var val = parseInt($input.val(), 10) || 1;
                if (val > 1) $input.val(val - 1).trigger('change');
            });

            // Quantity plus
            this.$wrapper.on('click', '.msf-qty-plus', function() {
                var $input = $(this).siblings('.msf-qty-input');
                var val = parseInt($input.val(), 10) || 1;
                var max = parseInt($input.attr('max'), 10);
                if (!max || val < max) $input.val(val + 1).trigger('change');
            });

            // Sync quantity to add-to-cart button data-quantity
            this.$wrapper.on('change', '.msf-qty-input', function() {
                var qty = parseInt($(this).val(), 10) || 1;
                $(this).closest('.msf-product-card__actions').find('.msf-product-card__button').attr('data-quantity', qty);
            });

            // URL popstate
            if (this.settings.enable_url_sync) {
                $(window).on('popstate', function() {
                    self.restoreFromUrl();
                    self.fetchProducts(false);
                });
            }

            // Listen for PartSearch custom event
            this.$wrapper[0].addEventListener('sfPartSearch', function(e) {
                var detail = e.detail || {};
                self.currentSearchType = detail.search_type || '';
                self.currentSearch = detail.search || '';
                self.$wrapper.find('.msf-search-input').val(self.currentSearch);
                self.currentPage = 1;
                self.fetchProducts(false);
            });
        },

        getFilters: function() {
            var filters = {};

            // Search
            var search = this.$wrapper.find('.msf-search-input').val();
            if (search) {
                filters.search = search;
            }

            // Taxonomies
            var taxonomies = {};

            // Selects
            this.$wrapper.find('select.msf-taxonomy-filter').each(function() {
                var tax = $(this).data('taxonomy');
                var val = $(this).val();
                if (val) {
                    taxonomies[tax] = val;
                }
            });

            // Radios
            this.$wrapper.find('input[type="radio"].msf-taxonomy-filter:checked').each(function() {
                var tax = $(this).data('taxonomy');
                var val = $(this).val();
                if (val) {
                    taxonomies[tax] = val;
                }
            });

            // Checkboxes
            var checkboxTaxonomies = {};
            this.$wrapper.find('input[type="checkbox"].msf-taxonomy-filter:checked').each(function() {
                var tax = $(this).data('taxonomy');
                var val = $(this).val();
                if (!checkboxTaxonomies[tax]) {
                    checkboxTaxonomies[tax] = [];
                }
                checkboxTaxonomies[tax].push(val);
            });
            $.extend(taxonomies, checkboxTaxonomies);

            if (Object.keys(taxonomies).length) {
                filters.taxonomies = taxonomies;
            }

            // Price (range slider)
            var $rangeWrapper = this.$wrapper.find('.msf-price-range-wrapper');
            if ($rangeWrapper.length) {
                var rangeMin = parseInt($rangeWrapper.data('min'), 10) || 0;
                var rangeMax = parseInt($rangeWrapper.data('max'), 10) || 0;
                var priceMin = parseInt(this.$wrapper.find('.msf-price-range-min').val(), 10);
                var priceMax = parseInt(this.$wrapper.find('.msf-price-range-max').val(), 10);
                if (priceMin > rangeMin) filters.price_min = priceMin;
                if (priceMax < rangeMax) filters.price_max = priceMax;
            }

            return filters;
        },

        initPriceRange: function() {
            var $wrapper = this.$wrapper.find('.msf-price-range-wrapper');
            if (!$wrapper.length) return;
            this.updatePriceRange();
        },

        updatePriceRange: function() {
            var $wrapper = this.$wrapper.find('.msf-price-range-wrapper');
            if (!$wrapper.length) return;

            var $min = this.$wrapper.find('.msf-price-range-min');
            var $max = this.$wrapper.find('.msf-price-range-max');
            var min = parseInt($min.val(), 10);
            var max = parseInt($max.val(), 10);
            var rangeMin = parseInt($wrapper.data('min'), 10) || 0;
            var rangeMax = parseInt($wrapper.data('max'), 10) || 1000;

            // Prevent handles from crossing
            if (min > max) {
                min = max;
                $min.val(min);
            }
            if (max < min) {
                max = min;
                $max.val(max);
            }

            // Update fill bar
            var range = rangeMax - rangeMin;
            var leftPercent = range > 0 ? ((min - rangeMin) / range) * 100 : 0;
            var rightPercent = range > 0 ? ((max - rangeMin) / range) * 100 : 100;
            this.$wrapper.find('.msf-price-range-fill').css({
                left: leftPercent + '%',
                width: (rightPercent - leftPercent) + '%'
            });

            // Update labels
            var currency = this.$wrapper.find('.msf-price-range-label-min').text().replace(/[\d\s]/g, '').trim() || 'kr';
            this.$wrapper.find('.msf-price-range-label-min').text(currency + ' ' + min);
            this.$wrapper.find('.msf-price-range-label-max').text(currency + ' ' + max);
        },

        fetchProducts: function(append) {
            var self = this;

            // Abort previous request
            if (this.xhr) {
                this.xhr.abort();
            }

            var filters = this.getFilters();

            var data = {
                action:         'sf_product_search',
                nonce:          this.nonce,
                search:         filters.search || this.currentSearch || '',
                search_type:    this.currentSearchType || '',
                taxonomies:     filters.taxonomies || {},
                price_min:      filters.price_min || '',
                price_max:      filters.price_max || '',
                orderby:        this.currentSort,
                posts_per_page: this.settings.posts_per_page || 12,
                paged:          this.currentPage,
                view:           this.currentView,
                settings: {
                    filter_position: this.settings.filter_position || 'top',
                    pagination_type: this.settings.pagination_type || 'numbered',
                    load_more_text:  this.settings.load_more_text || 'Load More',
                    grid_columns:    this.settings.grid_columns || '3',
                    show_thumbnail:     'yes',
                    show_title:         'yes',
                    show_price:         'yes',
                    show_description:   '',
                    show_category_badge:'yes',
                    show_tags:          '',
                    show_rating:        'yes',
                    show_stock_status:  '',
                    show_add_to_cart:   'yes',
                    button_text:        'Add to Cart',
                    show_quantity_selector: this.settings.show_quantity_selector || '',
                    cart_button_type: this.settings.cart_button_type || 'text'
                }
            };

            this.showLoading();

            if (this.settings.enable_url_sync) {
                this.pushUrl(filters);
            }

            this.xhr = $.post(this.ajaxUrl, data, function(response) {
                if (response.success) {
                    if (append) {
                        self.$results.find('.msf-products').append(
                            $(response.data.html).find('.msf-product-card')
                        );
                    } else {
                        self.$results.html(response.data.html);
                    }
                    self.$pagination.html(response.data.pagination);

                    if (self.settings.pagination_type === 'infinite_scroll') {
                        self.initInfiniteScroll();
                    }
                }
                self.hideLoading();
                self.xhr = null;
            }).fail(function(jqXHR, textStatus) {
                if (textStatus !== 'abort') {
                    self.hideLoading();
                }
                self.xhr = null;
            });
        },

        showLoading: function() {
            this.$overlay.show();
            this.$results.css('opacity', '0.5');
        },

        hideLoading: function() {
            this.$overlay.hide();
            this.$results.css('opacity', '1');
        },

        scrollToTop: function() {
            $('html, body').animate({
                scrollTop: this.$wrapper.offset().top - 50
            }, 300);
        },

        // Infinite scroll via IntersectionObserver
        initInfiniteScroll: function() {
            var self = this;
            var sentinel = this.$pagination.find('.msf-pagination__sentinel')[0];

            if (!sentinel || !('IntersectionObserver' in window)) return;

            if (this.observer) {
                this.observer.disconnect();
            }

            this.observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        self.currentPage = parseInt($(entry.target).data('page'), 10);
                        self.fetchProducts(true);
                        self.observer.disconnect();
                    }
                });
            }, { rootMargin: '200px' });

            this.observer.observe(sentinel);
        },

        // URL sync
        pushUrl: function(filters) {
            var params = new URLSearchParams();

            var searchVal = filters.search || this.currentSearch || '';
            if (searchVal) params.set('msf_search', searchVal);
            if (this.currentSearchType) params.set('msf_search_type', this.currentSearchType);
            if (filters.taxonomies) {
                for (var tax in filters.taxonomies) {
                    var val = filters.taxonomies[tax];
                    if (Array.isArray(val)) {
                        params.set('msf_tax_' + tax, val.join(','));
                    } else {
                        params.set('msf_tax_' + tax, val);
                    }
                }
            }
            if (filters.price_min) params.set('msf_price_min', filters.price_min);
            if (filters.price_max) params.set('msf_price_max', filters.price_max);
            if (this.currentSort !== (this.settings.default_sort || 'date')) {
                params.set('msf_sort', this.currentSort);
            }
            if (this.currentPage > 1) {
                params.set('msf_page', this.currentPage);
            }
            if (this.currentView !== (this.settings.default_view || 'grid')) {
                params.set('msf_view', this.currentView);
            }

            var qs = params.toString();
            var url = window.location.pathname + (qs ? '?' + qs : '');
            history.pushState(null, '', url);
        },

        restoreFromUrl: function() {
            var params = new URLSearchParams(window.location.search);

            // Search
            var search = params.get('msf_search') || '';
            this.currentSearch = search;
            this.$wrapper.find('.msf-search-input').val(search);

            // Search type
            this.currentSearchType = params.get('msf_search_type') || '';

            // Taxonomies
            var self = this;
            this.$wrapper.find('select.msf-taxonomy-filter').each(function() {
                var tax = $(this).data('taxonomy');
                var val = params.get('msf_tax_' + tax) || '';
                $(this).val(val);
            });

            this.$wrapper.find('input[type="radio"].msf-taxonomy-filter').each(function() {
                var tax = $(this).data('taxonomy');
                var val = params.get('msf_tax_' + tax) || '';
                $(this).prop('checked', $(this).val() === val);
            });

            this.$wrapper.find('input[type="checkbox"].msf-taxonomy-filter').each(function() {
                var tax = $(this).data('taxonomy');
                var urlVal = params.get('msf_tax_' + tax) || '';
                var vals = urlVal ? urlVal.split(',') : [];
                $(this).prop('checked', vals.indexOf($(this).val()) !== -1);
            });

            // Price (range slider)
            var $rangeWrapper = this.$wrapper.find('.msf-price-range-wrapper');
            if ($rangeWrapper.length) {
                var rangeMin = parseInt($rangeWrapper.data('min'), 10) || 0;
                var rangeMax = parseInt($rangeWrapper.data('max'), 10) || 0;
                var urlMin = params.get('msf_price_min');
                var urlMax = params.get('msf_price_max');
                this.$wrapper.find('.msf-price-range-min').val(urlMin ? parseInt(urlMin, 10) : rangeMin);
                this.$wrapper.find('.msf-price-range-max').val(urlMax ? parseInt(urlMax, 10) : rangeMax);
                this.updatePriceRange();
            }

            // Sort
            if (params.get('msf_sort')) {
                this.currentSort = params.get('msf_sort');
                this.$wrapper.find('.msf-sort-select').val(this.currentSort);
            }

            // Page
            this.currentPage = parseInt(params.get('msf_page'), 10) || 1;

            // View
            if (params.get('msf_view')) {
                this.currentView = params.get('msf_view');
                this.$wrapper.find('.msf-view-btn').removeClass('msf-view-btn--active');
                this.$wrapper.find('.msf-view-btn[data-view="' + this.currentView + '"]').addClass('msf-view-btn--active');
            }
        }
    };

    // Initialize for each widget instance
    function initWidget($scope) {
        var $wrapper = $scope.find('.msf-wrapper');
        if ($wrapper.length) {
            new SFilterProductSearch($wrapper);
        }
    }

    // Elementor frontend hook
    $(window).on('elementor/frontend/init', function() {
        if (typeof elementorFrontend !== 'undefined' && elementorFrontend.hooks) {
            elementorFrontend.hooks.addAction(
                'frontend/element_ready/sfilter-product-search-filter.default',
                initWidget
            );
        }
    });

    // Fallback for non-Elementor preview (e.g., cached pages)
    $(document).ready(function() {
        if (typeof elementorFrontend === 'undefined') {
            $('.msf-wrapper').each(function() {
                new SFilterProductSearch($(this));
            });
        }
    });

})(jQuery);

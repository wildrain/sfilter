/**
 * Part Search Widget JavaScript
 *
 * @package SFilter\Elementor\PartSearch
 */

(function($) {
    'use strict';

    /**
     * Part Search Widget Handler
     */
    class SFPartSearch {
        constructor($wrapper) {
            this.$wrapper = $wrapper;
            this.shopUrl = $wrapper.data('shop-url') || '/shop';
            this.searchParam = $wrapper.data('search-param') || 'item';
            this.maxParts = parseInt($wrapper.data('max-parts'), 10) || 15;

            this.init();
        }

        init() {
            this.bindTabEvents();
            this.bindFormEvents();
            this.bindResetEvents();
            this.bindInputEvents();
            this.restoreFromUrl();

            // Handle browser back/forward
            $(window).on('popstate', () => this.restoreFromUrl());
        }

        /**
         * Restore search state from URL parameters
         */
        restoreFromUrl() {
            const params = new URLSearchParams(window.location.search);
            const search = params.get('msf_search');
            const searchType = params.get('msf_search_type');

            if (!search || !searchType) {
                return;
            }

            const tabMap = {
                part: 'part-search',
                multipart: 'multipart-search',
                attribute: 'attribute-search',
                equipment: 'equipment-search'
            };

            const tabId = tabMap[searchType];
            if (!tabId) {
                return;
            }

            // Activate the correct tab
            const $tabButton = this.$wrapper.find('.sf-tab-button[data-tab="' + tabId + '"]');
            if ($tabButton.length) {
                this.$wrapper.find('.sf-tab-button').removeClass('active');
                $tabButton.addClass('active');
                this.$wrapper.find('.sf-tab-content').removeClass('active');
                this.$wrapper.find('[data-tab-content="' + tabId + '"]').addClass('active');
            }

            // Prefill the appropriate input
            const $tabContent = this.$wrapper.find('[data-tab-content="' + tabId + '"]');
            if (searchType === 'part') {
                $tabContent.find('.sf-search-input').val(search);
            } else if (searchType === 'multipart') {
                $tabContent.find('.sf-search-textarea').val(search.replace(/,/g, '\n'));
                this.updateResetLinkState($tabContent.find('.sf-search-form'));
            }
        }

        /**
         * Handle tab switching
         */
        bindTabEvents() {
            const self = this;

            this.$wrapper.on('click', '.sf-tab-button', function(e) {
                e.preventDefault();

                const $button = $(this);
                const tabId = $button.data('tab');

                // Update active tab button
                self.$wrapper.find('.sf-tab-button').removeClass('active');
                $button.addClass('active');

                // Update active tab content
                self.$wrapper.find('.sf-tab-content').removeClass('active');
                self.$wrapper.find('[data-tab-content="' + tabId + '"]').addClass('active');
            });
        }

        /**
         * Handle form submissions
         */
        bindFormEvents() {
            const self = this;

            this.$wrapper.on('submit', '.sf-search-form', function(e) {
                e.preventDefault();

                const $form = $(this);
                const searchType = $form.data('search-type');

                switch (searchType) {
                    case 'part':
                        self.handlePartSearch($form);
                        break;
                    case 'multipart':
                        self.handleMultipartSearch($form);
                        break;
                    case 'attribute':
                        self.handleAttributeSearch($form);
                        break;
                    case 'equipment':
                        self.handleEquipmentSearch($form);
                        break;
                }
            });
        }

        /**
         * Handle reset link clicks
         */
        bindResetEvents() {
            const self = this;

            this.$wrapper.on('click', '.sf-reset-link', function(e) {
                e.preventDefault();

                if ($(this).hasClass('disabled')) {
                    return;
                }

                const $form = $(this).closest('.sf-search-form');

                // Reset all form fields
                $form.find('input[type="text"], textarea').val('');
                $form.find('select').each(function() {
                    $(this).prop('selectedIndex', 0);
                });

                // Hide error message
                $form.find('.sf-max-parts-error').attr('hidden', true);

                // Disable reset link
                $(this).addClass('disabled');
            });
        }

        /**
         * Bind input change events for reset link state
         */
        bindInputEvents() {
            const self = this;

            this.$wrapper.on('input change', '.sf-search-input, .sf-search-textarea, .sf-search-select', function() {
                const $form = $(this).closest('.sf-search-form');
                self.updateResetLinkState($form);
            });
        }

        /**
         * Update reset link enabled/disabled state
         */
        updateResetLinkState($form) {
            const $resetLink = $form.find('.sf-reset-link');
            let hasValue = false;

            // Check text inputs and textareas
            $form.find('.sf-search-input, .sf-search-textarea').each(function() {
                if ($(this).val().trim() !== '') {
                    hasValue = true;
                    return false;
                }
            });

            // Check selects (if not on first option)
            if (!hasValue) {
                $form.find('.sf-search-select').each(function() {
                    if ($(this).prop('selectedIndex') > 0) {
                        hasValue = true;
                        return false;
                    }
                });
            }

            if (hasValue) {
                $resetLink.removeClass('disabled');
            } else {
                $resetLink.addClass('disabled');
            }
        }

        /**
         * Trigger same-page search via custom event on .msf-wrapper
         * Returns true if consumed (same page), false if not found
         */
        triggerSamePageSearch(search, searchType) {
            var msfWrapper = document.querySelector('.msf-wrapper');
            if (!msfWrapper) return false;

            msfWrapper.dispatchEvent(new CustomEvent('sfPartSearch', {
                detail: { search: search, search_type: searchType }
            }));

            // Update URL without reload
            var params = new URLSearchParams(window.location.search);
            params.set('msf_search', search);
            params.set('msf_search_type', searchType);
            history.pushState(null, '', window.location.pathname + '?' + params.toString());

            return true;
        }

        /**
         * Handle single part search
         */
        handlePartSearch($form) {
            const searchTerm = $form.find('.sf-search-input').val().trim();

            if (!searchTerm) {
                // Clear URL search params and reset ProductSearchFilter
                var msfWrapper = document.querySelector('.msf-wrapper');
                if (msfWrapper) {
                    msfWrapper.dispatchEvent(new CustomEvent('sfPartSearch', {
                        detail: { search: '', search_type: '' }
                    }));
                }
                var params = new URLSearchParams(window.location.search);
                params.delete('msf_search');
                params.delete('msf_search_type');
                var newUrl = params.toString()
                    ? window.location.pathname + '?' + params.toString()
                    : window.location.pathname;
                history.pushState(null, '', newUrl);
                return;
            }

            // Try same-page integration first
            if (this.triggerSamePageSearch(searchTerm, 'part')) {
                return;
            }

            const url = this.buildUrl({
                msf_search: searchTerm,
                msf_search_type: 'part'
            });

            window.location.href = url;
        }

        /**
         * Handle multipart search
         */
        handleMultipartSearch($form) {
            const $textarea = $form.find('.sf-search-textarea');
            const $errorMsg = $form.find('.sf-max-parts-error');
            const rawValue = $textarea.val().trim();

            if (!rawValue) {
                // Clear URL search params and reset ProductSearchFilter
                var msfWrapper = document.querySelector('.msf-wrapper');
                if (msfWrapper) {
                    msfWrapper.dispatchEvent(new CustomEvent('sfPartSearch', {
                        detail: { search: '', search_type: '' }
                    }));
                }
                var params = new URLSearchParams(window.location.search);
                params.delete('msf_search');
                params.delete('msf_search_type');
                var newUrl = params.toString()
                    ? window.location.pathname + '?' + params.toString()
                    : window.location.pathname;
                history.pushState(null, '', newUrl);
                return;
            }

            // Parse part numbers (split by newlines or commas)
            let parts = rawValue
                .split(/[\n,]+/)
                .map(p => p.trim())
                .filter(p => p !== '');

            // Check max parts limit
            if (parts.length > this.maxParts) {
                $errorMsg.removeAttr('hidden');
                return;
            }

            $errorMsg.attr('hidden', true);

            const joinedParts = parts.join(',');

            // Try same-page integration first
            if (this.triggerSamePageSearch(joinedParts, 'multipart')) {
                return;
            }

            const url = this.buildUrl({
                msf_search: joinedParts,
                msf_search_type: 'multipart'
            });

            window.location.href = url;
        }

        /**
         * Handle attribute search
         */
        handleAttributeSearch($form) {
            const params = {
                search_type: 'attribute'
            };

            $form.find('.sf-search-select').each(function() {
                const $select = $(this);
                const name = $select.attr('name');
                const value = $select.val();

                // Only include if not the first/default option
                if ($select.prop('selectedIndex') > 0 && value) {
                    params[name] = value;
                }
            });

            // Check if any attributes were selected
            if (Object.keys(params).length === 1) {
                return;
            }

            const url = this.buildUrl(params);
            window.location.href = url;
        }

        /**
         * Handle equipment search
         */
        handleEquipmentSearch($form) {
            const params = {
                search_type: 'equipment'
            };

            $form.find('.sf-search-select').each(function() {
                const $select = $(this);
                const name = $select.attr('name');
                const value = $select.val();

                // Only include if not the first/default option
                if ($select.prop('selectedIndex') > 0 && value) {
                    params[name] = value;
                }
            });

            // Check if any fields were selected
            if (Object.keys(params).length === 1) {
                return;
            }

            const url = this.buildUrl(params);
            window.location.href = url;
        }

        /**
         * Build URL with query parameters
         */
        buildUrl(params) {
            let url = this.shopUrl;

            // Handle relative URLs
            if (!url.startsWith('http') && !url.startsWith('/')) {
                url = '/' + url;
            }

            const queryString = Object.keys(params)
                .map(key => encodeURIComponent(key) + '=' + encodeURIComponent(params[key]))
                .join('&');

            if (queryString) {
                url += (url.includes('?') ? '&' : '?') + queryString;
            }

            return url;
        }
    }

    /**
     * Initialize on document ready
     */
    $(document).ready(function() {
        $('.sf-search-wrapper').each(function() {
            new SFPartSearch($(this));
        });
    });

    /**
     * Reinitialize for Elementor editor
     */
    $(window).on('elementor/frontend/init', function() {
        if (typeof elementorFrontend !== 'undefined') {
            elementorFrontend.hooks.addAction('frontend/element_ready/sfilter_part_search.default', function($scope) {
                const $wrapper = $scope.find('.sf-search-wrapper');
                if ($wrapper.length) {
                    new SFPartSearch($wrapper);
                }
            });
        }
    });

})(jQuery);

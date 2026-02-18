<?php

namespace SFilter\Quote;

/**
 * Rename "Orders" to "Quotes" throughout WooCommerce admin
 */
class Labels
{
    public function __construct()
    {
        add_filter('woocommerce_register_shop_order_post_type', [$this, 'modify_post_type_labels']);
        add_filter('gettext', [$this, 'replace_order_text'], 20, 3);
        add_filter('ngettext', [$this, 'replace_order_plural_text'], 20, 5);
        add_action('admin_menu', [$this, 'rename_submenu'], 999);
    }

    /**
     * Modify post type labels for shop_order
     *
     * @param array $args Post type arguments
     * @return array
     */
    public function modify_post_type_labels($args)
    {
        $args['labels'] = [
            'name'                  => __('Quotes', 'sfilter'),
            'singular_name'         => __('Quote', 'sfilter'),
            'add_new'               => __('Add Quote', 'sfilter'),
            'add_new_item'          => __('Add New Quote', 'sfilter'),
            'edit'                  => __('Edit', 'sfilter'),
            'edit_item'             => __('Edit Quote', 'sfilter'),
            'new_item'              => __('New Quote', 'sfilter'),
            'view_item'             => __('View Quote', 'sfilter'),
            'view_items'            => __('View Quotes', 'sfilter'),
            'search_items'          => __('Search Quotes', 'sfilter'),
            'not_found'             => __('No quotes found', 'sfilter'),
            'not_found_in_trash'    => __('No quotes found in trash', 'sfilter'),
            'parent'                => __('Parent Quote', 'sfilter'),
            'menu_name'             => __('Quotes', 'sfilter'),
            'filter_items_list'     => __('Filter quotes', 'sfilter'),
            'items_list_navigation' => __('Quotes navigation', 'sfilter'),
            'items_list'            => __('Quotes list', 'sfilter'),
        ];

        return $args;
    }

    /**
     * Replace "Order" with "Quote" in translated strings
     *
     * @param string $translated Translated text
     * @param string $text Original text
     * @param string $domain Text domain
     * @return string
     */
    public function replace_order_text($translated, $text, $domain)
    {
        if ($domain !== 'woocommerce') {
            return $translated;
        }

        // Only apply on order-related admin screens
        if (!$this->is_order_screen()) {
            return $translated;
        }

        $replacements = [
            'Order'        => 'Quote',
            'order'        => 'quote',
            'Orders'       => 'Quotes',
            'orders'       => 'quotes',
            'Order #'      => 'Quote #',
            'Order notes'  => 'Quote notes',
            'Order status' => 'Quote status',
            'Order total'  => 'Quote total',
            'Order date'   => 'Quote date',
        ];

        foreach ($replacements as $search => $replace) {
            if (strpos($translated, $search) !== false) {
                $translated = str_replace($search, $replace, $translated);
            }
        }

        return $translated;
    }

    /**
     * Replace "Order/Orders" in plural strings
     *
     * @param string $translated Translated text
     * @param string $single Singular form
     * @param string $plural Plural form
     * @param int $number Number
     * @param string $domain Text domain
     * @return string
     */
    public function replace_order_plural_text($translated, $single, $plural, $number, $domain)
    {
        if ($domain !== 'woocommerce') {
            return $translated;
        }

        if (!$this->is_order_screen()) {
            return $translated;
        }

        $replacements = [
            'Order'  => 'Quote',
            'order'  => 'quote',
            'Orders' => 'Quotes',
            'orders' => 'quotes',
        ];

        foreach ($replacements as $search => $replace) {
            if (strpos($translated, $search) !== false) {
                $translated = str_replace($search, $replace, $translated);
            }
        }

        return $translated;
    }

    /**
     * Rename the WooCommerce Orders submenu item
     */
    public function rename_submenu()
    {
        global $submenu;

        if (isset($submenu['woocommerce'])) {
            foreach ($submenu['woocommerce'] as $key => $item) {
                if (isset($item[2]) && $item[2] === 'edit.php?post_type=shop_order') {
                    $submenu['woocommerce'][$key][0] = __('Quotes', 'sfilter');
                    break;
                }
                // HPOS support
                if (isset($item[2]) && $item[2] === 'wc-orders') {
                    $submenu['woocommerce'][$key][0] = __('Quotes', 'sfilter');
                    break;
                }
            }
        }
    }

    /**
     * Check if current screen is an order-related screen
     *
     * @return bool
     */
    private function is_order_screen()
    {
        if (!function_exists('get_current_screen')) {
            return false;
        }

        $screen = get_current_screen();
        if (!$screen) {
            return false;
        }

        $order_screens = [
            'edit-shop_order',
            'shop_order',
            'woocommerce_page_wc-orders',
        ];

        return in_array($screen->id, $order_screens, true);
    }
}

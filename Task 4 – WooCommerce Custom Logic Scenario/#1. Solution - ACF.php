<?php
// 1. REGISTER ACF FIELD GROUP (runs once)
if( function_exists('acf_add_local_field_group') ) {
    acf_add_local_field_group(array(
        'key' => 'group_hide_product_categories',
        'title' => 'Category Visibility',
        'fields' => array(
            array(
                'key' => 'field_hide_in_listings',
                'label' => 'Hide in Listings',
                'name' => 'hide_in_listings',
                'type' => 'true_false',
                'default_value' => 0,
                'instructions' => 'Check to hide from shop pages, menus, admin menu screen, and custom links.',
                'required' => 0,
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'taxonomy',
                    'operator' => '==',
                    'value' => 'product_cat',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
    ));
}

// 2. HELPER FUNCTION - Get hidden category IDs (unchanged)
function get_hidden_product_cat_ids() {
    static $cache = [];
    if (isset($cache['ids'])) return $cache['ids'];

    global $wpdb;
    $ids = $wpdb->get_col($wpdb->prepare("
        SELECT t.term_id
        FROM {$wpdb->terms} t
        INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
        INNER JOIN {$wpdb->termmeta} tm ON t.term_id = tm.term_id
        WHERE tt.taxonomy = 'product_cat'
        AND tm.meta_key = 'hide_in_listings'
        AND tm.meta_value = %s
    ", '1'));

    $cache['ids'] = $ids ? $ids : [];
    return $cache['ids'];
}

// 3. HIDE FROM SHOP PAGE ONLY (manual URLs work)
add_action('pre_get_posts', function($query) {
    if (!is_admin() && $query->is_main_query() && is_shop() && !is_product_category()) {
        $hidden_ids = get_hidden_product_cat_ids();
        if (!empty($hidden_ids)) {
            $tax_query = (array) $query->get('tax_query');
            $tax_query[] = ['taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $hidden_ids, 'operator' => 'NOT IN'];
            $query->set('tax_query', $tax_query);
        }
    }
});

// 4. HIDE FROM ALL MENUS + CUSTOM LINKS IF TRIED TO BE ADDED
add_filter('wp_get_nav_menu_items', function($items, $menu, $args) {
    $hidden_ids = get_hidden_product_cat_ids();
    if (!empty($hidden_ids)) {
        foreach ($items as $key => $item) {
            // Product category menu items
            if ($item->object === 'product_cat' && in_array($item->object_id, $hidden_ids)) {
                unset($items[$key]);
            }
            // Custom links pointing to hidden categories
            elseif ($item->object === 'custom' && !empty($item->url)) {
                $url_parts = parse_url($item->url);
                if (isset($url_parts['path']) && preg_match('#/product-category/([^/]+)/?#', $url_parts['path'], $matches)) {
                    $category = get_term_by('slug', $matches[1], 'product_cat');
                    if ($category && in_array($category->term_id, $hidden_ids)) {
                        unset($items[$key]);
                    }
                }
            }
        }
    }
    return array_values($items);
}, 10, 3);

// 5. HIDE FROM MENU BUILDER
add_filter('wp_nav_menu_terms_checklist_args', function($args, $post_id) {
    if (isset($args['taxonomy']) && $args['taxonomy'] === 'product_cat') {
        $hidden_ids = get_hidden_product_cat_ids();
        if (!empty($hidden_ids)) {
            $args['exclude'] = isset($args['exclude']) ? array_merge($args['exclude'], $hidden_ids) : $hidden_ids;
        }
    }
    return $args;
}, 10, 2);

// 6. HIDE FROM ADMIN MENUS SCREEN
add_filter('get_terms', function($terms, $taxonomies, $args) {
    if (!is_admin()) return $terms;
    global $pagenow;
    if ($pagenow !== 'nav-menus.php') return $terms;
    if (!in_array('product_cat', (array) $taxonomies)) return $terms;

    $hidden_ids = get_hidden_product_cat_ids();
    if (empty($hidden_ids) || !is_array($terms)) return $terms;

    foreach ($terms as $key => $term) {
        if (in_array($term->term_id, $hidden_ids)) {
            unset($terms[$key]);
        }
    }
    return array_values($terms);
}, 5, 3);
?>
<?
/**
 * Hide Product Categories with Checkbox - MANUAL URLS WORK
 */

// 1. ADD CHECKBOX TO CATEGORY FORMS (unchanged)
add_action('product_cat_add_form_fields', function($taxonomy) { ?>
    <div class="form-field term-group">
        <label for="hide_in_listings"><?php _e('Hide in Listings', 'your-textdomain'); ?></label>
        <input type="checkbox" id="hide_in_listings" name="hide_in_listings" value="1">
        <p class="description"><?php _e('Check to hide from shop pages, menus, and admin menu screen.', 'your-textdomain'); ?></p>
    </div>
<?php });

add_action('product_cat_edit_form_fields', function($term) {
    $hidden = get_term_meta($term->term_id, 'hide_in_listings', true); ?>
    <tr class="form-field term-group-wrap">
        <th scope="row"><label for="hide_in_listings"><?php _e('Hide in Listings', 'your-textdomain'); ?></label></th>
        <td>
            <input type="checkbox" id="hide_in_listings" name="hide_in_listings" value="1" <?php checked($hidden, 1); ?>>
            <p class="description"><?php _e('Check to hide from shop pages, menus, and admin menu screen.', 'your-textdomain'); ?></p>
        </td>
    </tr>
<?php });

// 2. SAVE CHECKBOX VALUE (unchanged)
add_action('created_product_cat', function($term_id) {
    if (isset($_POST['hide_in_listings'])) {
        update_term_meta($term_id, 'hide_in_listings', 1);
    }
}, 10, 1);

add_action('edited_product_cat', function($term_id) {
    $value = isset($_POST['hide_in_listings']) ? 1 : 0;
    update_term_meta($term_id, 'hide_in_listings', $value);
}, 10, 1);

// 3. HELPER FUNCTION (unchanged)
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

// 4. FIXED: Only hide from MAIN SHOP PAGE, not individual category pages
add_action('pre_get_posts', function($query) {
    if (!is_admin() && $query->is_main_query()) {
        // ONLY target main shop page, NOT individual category pages
        if (is_shop() && !is_product_category()) {
            $hidden_ids = get_hidden_product_cat_ids();
            if (!empty($hidden_ids)) {
                $tax_query = (array) $query->get('tax_query');
                $tax_query[] = [
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => $hidden_ids,
                    'operator' => 'NOT IN'
                ];
                $query->set('tax_query', $tax_query);
            }
        }
    }
});

// 5-7. All other filters unchanged (menus + admin work perfectly)
add_filter('wp_get_nav_menu_items', function($items, $menu, $args) {
    $hidden_ids = get_hidden_product_cat_ids();
    if (!empty($hidden_ids)) {
        foreach ($items as $key => $item) {
            if ($item->object === 'product_cat' && in_array($item->object_id, $hidden_ids)) {
                unset($items[$key]);
            }
        }
    }
    return array_values($items);
}, 10, 3);

add_filter('wp_nav_menu_terms_checklist_args', function($args, $post_id) {
    if (isset($args['taxonomy']) && $args['taxonomy'] === 'product_cat') {
        $hidden_ids = get_hidden_product_cat_ids();
        if (!empty($hidden_ids)) {
            $args['exclude'] = isset($args['exclude'])
                ? array_merge($args['exclude'], $hidden_ids)
                : $hidden_ids;
        }
    }
    return $args;
}, 10, 2);

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


// 8. HIDE CUSTOM LINKS to hidden categories from frontend menus
add_filter('wp_get_nav_menu_items', function($items, $menu, $args) {
    $hidden_ids = get_hidden_product_cat_ids();
    if (!empty($hidden_ids)) {
        foreach ($items as $key => $item) {
            // Check both product_cat objects AND custom links pointing to hidden categories
            if ($item->object === 'product_cat' && in_array($item->object_id, $hidden_ids)) {
                unset($items[$key]);
            }
            // NEW: Check custom links URLs
            elseif ($item->object === 'custom' && !empty($item->url)) {
                $url_parts = parse_url($item->url);
                if (isset($url_parts['path'])) {
                    // Extract category slug from URL like /product-category/hidden-slug/
                    if (preg_match('#/product-category/([^/]+)/?#', $url_parts['path'], $matches)) {
                        $cat_slug = $matches[1];
                        $category = get_term_by('slug', $cat_slug, 'product_cat');
                        if ($category && in_array($category->term_id, $hidden_ids)) {
                            unset($items[$key]);
                        }
                    }
                }
            }
        }
    }
    return array_values($items);
}, 10, 3);
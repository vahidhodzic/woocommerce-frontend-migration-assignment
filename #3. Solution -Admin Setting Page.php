<?
// 1. ADMIN SETTINGS PAGE
add_action('admin_menu', function() {
    add_submenu_page('edit.php?post_type=product', 'Hidden Categories', 'Hidden Cats', 'manage_options', 'hidden-product-cats', 'hidden_cats_admin_page');
});

function hidden_cats_admin_page() {
    // FIXED: Handle textarea input properly
    if (isset($_POST['save_hidden_cats'])) {
        $input = sanitize_textarea_field($_POST['categories']);
        $categories = array_filter(array_map('trim', explode("\n", $input)));
        update_option('hidden_product_categories', $categories);
        echo '<div class="notice notice-success"><p>✅ Hidden categories saved!</p></div>';
    }

    $hidden = get_option('hidden_product_categories', []);
    $all_cats = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);
    ?>

    <div class="wrap">
        <h1>🕵️ Hidden Product Categories</h1>
        <form method="post">
            <?php wp_nonce_field('hidden_cats_save', 'hidden_cats_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">Hidden Category Slugs</th>
                    <td>
                        <textarea name="categories" rows="10" cols="60" class="large-text code"><?php echo esc_textarea(implode("\n", $hidden)); ?></textarea>
                        <p class="description">
                            Enter one category slug per line. These categories will be hidden from:<br>
                            • Shop page • Navigation menus • Menu builder • Admin menu screen
                        </p>
                        <p><strong>Examples:</strong></p>
                        <textarea readonly rows="3" cols="60" class="large-text code" style="background:#f1f1f1"><?php
                            echo esc_textarea("secret-sale\nwholesale-only\npartner-deals\nhidden-promo");
                        ?></textarea>
                    </td>
                </tr>
            </table>
            <?php submit_button('💾 Save Hidden Categories', 'primary', 'save_hidden_cats'); ?>
        </form>

        <h2>📊 Current Status</h2>
        <?php if (empty($hidden)): ?>
            <p class="notice notice-info"><strong>No categories hidden</strong> - All categories visible everywhere</p>
        <?php else: ?>
            <p><strong>🔒 Hidden Categories (<?php echo count($hidden); ?>):</strong>
                <code><?php echo esc_html(implode('</code>, <code>', $hidden)); ?></code>
            </p>
            <p><strong>✅ Verified:</strong> <?php echo count(get_hidden_categories()); ?> categories found in database</p>
        <?php endif; ?>
    </div>
    <?php
}

// 2. CUSTOM QUERY HELPER - FIXED
function get_hidden_categories($return = 'ids') {
    static $cache = [];
    $hash = md5($return);

    if (!isset($cache[$hash])) {
        $slugs = get_option('hidden_product_categories', []);
        if (empty($slugs)) {
            $cache[$hash] = [];
            return [];
        }

        global $wpdb;
        $slugs_placeholder = implode("','", array_fill(0, count($slugs), '%s'));
        $ids = $wpdb->get_col($wpdb->prepare("
            SELECT t.term_id
            FROM {$wpdb->terms} t
            INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
            WHERE tt.taxonomy = 'product_cat'
            AND t.slug IN ($slugs_placeholder)
        ", ...$slugs));

        $cache[$hash] = $ids ?: [];
    }
    return $cache[$hash];
}

// 3. APPLY TO ALL LOCATIONS (Same as ACF solution)
add_action('pre_get_posts', function($query) {
    if (!is_admin() && $query->is_main_query() && is_shop() && !is_product_category()) {
        $hidden = get_hidden_categories();
        if ($hidden) {
            $tax_query = (array) $query->get('tax_query');
            $tax_query[] = ['taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $hidden, 'operator' => 'NOT IN'];
            $query->set('tax_query', $tax_query);
        }
    }
});

add_filter('wp_get_nav_menu_items', function($items, $menu, $args) {
    $hidden = get_hidden_categories();
    if (!empty($hidden)) {
        foreach ($items as $key => $item) {
            if ($item->object === 'product_cat' && in_array($item->object_id, $hidden)) {
                unset($items[$key]);
            } elseif ($item->object === 'custom' && !empty($item->url)) {
                $url_parts = parse_url($item->url);
                if (isset($url_parts['path']) && preg_match('#/product-category/([^/]+)/?#', $url_parts['path'], $matches)) {
                    $category = get_term_by('slug', $matches[1], 'product_cat');
                    if ($category && in_array($category->term_id, $hidden)) {
                        unset($items[$key]);
                    }
                }
            }
        }
    }
    return array_values($items);
}, 10, 3);

add_filter('wp_nav_menu_terms_checklist_args', function($args, $post_id) {
    if (isset($args['taxonomy']) && $args['taxonomy'] === 'product_cat') {
        $hidden = get_hidden_categories();
        if (!empty($hidden)) {
            $args['exclude'] = isset($args['exclude']) ? array_merge($args['exclude'], $hidden) : $hidden;
        }
    }
    return $args;
}, 10, 2);

add_filter('get_terms', function($terms, $taxonomies, $args) {
    if (!is_admin()) return $terms;
    global $pagenow;
    if ($pagenow !== 'nav-menus.php') return $terms;
    if (!in_array('product_cat', (array) $taxonomies)) return $terms;

    $hidden = get_hidden_categories();
    if (empty($hidden) || !is_array($terms)) return $terms;

    foreach ($terms as $key => $term) {
        if (in_array($term->term_id, $hidden)) {
            unset($terms[$key]);
        }
    }
    return array_values($terms);
}, 5, 3);
?>
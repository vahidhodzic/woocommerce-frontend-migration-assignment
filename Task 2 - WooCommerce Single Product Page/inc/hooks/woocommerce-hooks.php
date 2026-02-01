<?php
/**
 * WooCommerce Single Product Hooks
 * @package Forga
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Remove WooCommerce defaults
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 15 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

// Custom hooks with priority order
add_action( 'woocommerce_single_product_summary', 'forga_product_title', 5 );
function forga_product_title() {
    global $product;
    $title = $product->get_name();
    if ( $product->is_on_sale() ) {
        $title .= ' <span class="sale-badge">Sale</span>';
    }
    printf( '<h1 class="product_title">%s</h1>', esc_html( $title ) );
}

add_action( 'woocommerce_single_product_summary', 'forga_product_price', 10 );
function forga_product_price() {
    wc_get_template_part( 'single-product/price' );
}

add_action( 'woocommerce_single_product_summary', 'forga_product_short_description', 20 );
function forga_product_short_description() {
    woocommerce_template_single_excerpt();
}

add_action( 'woocommerce_single_product_summary', 'forga_product_add_to_cart', 30 );
function forga_product_add_to_cart() {
    global $product;
    if ( ! $product->is_purchasable() || ! $product->is_in_stock() ) {
        return;
    }
    $add_to_cart_path = $product->is_type( 'simple' )
        ? 'single-product/add-to-cart/simple'
        : 'single-product/add-to-cart/variable';
    wc_get_template_part( $add_to_cart_path );
}

add_action( 'woocommerce_single_product_summary', 'forga_product_meta', 40 );
function forga_product_meta() {
    woocommerce_template_single_meta();
}

// Gallery wrapper
add_action( 'woocommerce_before_single_product_summary', 'forga_gallery_wrapper', 5 );
function forga_gallery_wrapper() {
    echo '<div class="product-gallery-wrapper">';
    woocommerce_show_product_images();
    echo '</div>';
}

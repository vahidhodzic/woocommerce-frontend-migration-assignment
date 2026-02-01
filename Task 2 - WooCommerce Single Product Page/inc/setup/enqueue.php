<?php
/**
 * Enqueue Scripts and Styles
 */
function forga_enqueue_product_assets() {
    if ( is_product() ) {
        wp_enqueue_style( 'forga-woocommerce', get_stylesheet_directory_uri() . '/assets/css/woocommerce.css', array(), '1.0.0' );
        wp_enqueue_script( 'forga-product', get_stylesheet_directory_uri() . '/assets/js/product.js', array( 'jquery' ), '1.0.0', true );

        // Localize script for AJAX
        wp_localize_script( 'forga-product', 'forga_ajax', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'forga_nonce' )
        ));
    }
}
add_action( 'wp_enqueue_scripts', 'forga_enqueue_product_assets' );

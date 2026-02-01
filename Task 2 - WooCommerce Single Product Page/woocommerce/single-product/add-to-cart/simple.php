<?php
/**
 * Simple Product Add to Cart
 */
global $product;
if ( $product && $product->is_type( 'simple' ) ) {
    woocommerce_simple_add_to_cart();
}
?>

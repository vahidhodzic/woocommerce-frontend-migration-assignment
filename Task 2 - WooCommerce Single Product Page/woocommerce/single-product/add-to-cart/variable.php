<?php
/**
 * Variable Product Add to Cart
 */
global $product;
if ( $product && $product->is_type( 'variable' ) ) {
    woocommerce_variable_add_to_cart();
}
?>

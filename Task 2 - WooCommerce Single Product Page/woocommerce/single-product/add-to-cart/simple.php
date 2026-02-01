<?php
/**
 * Simple Product Add to Cart
 */
global $product;
if ( $product->is_purchasable() && $product->is_in_stock() ) :
    woocommerce_simple_add_to_cart();
endif;
?>

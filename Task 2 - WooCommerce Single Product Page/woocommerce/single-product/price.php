<?php
/**
 * Single Product Price
 */
global $product;
if ( $product->get_price_html() ) : ?>
    <div class="forga-single-product-price mb-3 fw-bold" data-unit-price="<?php echo esc_attr( $product->get_price() ); ?>">
        <?php echo wp_kses_post( $product->get_price_html() ); ?>
    </div>
<?php endif; ?>

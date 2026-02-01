<?php
/**
 * The template for displaying product content in the single-product.php template
 * @package Forga
 */
defined( 'ABSPATH' ) || exit;
global $product;
?>

<article <?php wc_product_class( 'forga-single-product', $product ); ?>>

    <?php do_action( 'woocommerce_before_single_product' ); ?>

    <div class="forga-single-product-container">

        <!-- Product Images + Gallery -->
        <div class="product-images-section">
            <?php do_action( 'woocommerce_before_single_product_summary' ); ?>
        </div>

        <!-- Product Summary -->
        <div class="forga-single-product__summary">
            <?php get_template_part( 'template-parts/product/product-summary' ); ?>
        </div>

    </div>

    <?php do_action( 'woocommerce_after_single_product_summary' ); ?>
    <?php do_action( 'woocommerce_after_single_product' ); ?>

</article>

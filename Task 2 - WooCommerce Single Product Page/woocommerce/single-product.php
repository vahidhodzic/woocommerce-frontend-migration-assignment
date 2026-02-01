<?php
/**
 * The Template for displaying all single products
 * @package Forga
 */
defined( 'ABSPATH' ) || exit;
get_header( 'shop' );
?>

<?php do_action( 'woocommerce_before_main_content' ); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php
        while ( have_posts() ) :
            the_post();
            wc_get_template_part( 'content', 'single-product' );
        endwhile;
        ?>
    </main>
</div>

<?php do_action( 'woocommerce_after_main_content' ); ?>
<?php get_footer( 'shop' ); ?>

<?php
/**
 * @package Forga
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php
// Load site header (with Woo-aware variants handled here)
if ( is_checkout() ) {
    get_template_part( 'template-parts/header/site-header', 'checkout' );
} elseif ( is_cart() ) {
    get_template_part( 'template-parts/header/site-header', 'cart' );
} elseif ( is_product() ) {
    get_template_part( 'template-parts/header/site-header', 'product' );
} else {
    get_template_part( 'template-parts/header/site-header' );
}

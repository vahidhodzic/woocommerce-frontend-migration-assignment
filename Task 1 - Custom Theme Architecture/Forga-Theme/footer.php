<?php
/**
 * Footer
 *
 * @package Forga
 */

defined( 'ABSPATH' ) || exit;
?>

<?php
if ( is_checkout() ) {
    get_template_part( 'template-parts/footer/site-footer', 'checkout' );
} else {
    get_template_part( 'template-parts/footer/site-footer' );
}
?>

<?php wp_footer(); ?>
</body>
</html>

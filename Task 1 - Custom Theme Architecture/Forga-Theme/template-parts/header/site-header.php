<?php
/**
 * Main site header
 *
 * @package Forga
 */

defined( 'ABSPATH' ) || exit;
?>

<header class="<?php echo esc_attr( yourtheme_header_classes() ); ?>">

    <div class="site-header__inner">

        <!-- Logo -->
        <div class="site-header__logo">
            <?php get_template_part( 'template-parts/header/logo' ); ?>
        </div>

        <!-- Primary navigation -->
        <nav class="site-header__nav">
            <?php get_template_part( 'template-parts/header/primary-nav' ); ?>
        </nav>

        <!-- Header actions -->
        <div class="site-header__actions">

            <?php
            // Search (optional)
            get_template_part( 'template-parts/header/search' );

            // Account links
            get_template_part( 'template-parts/header/account-links' );

            // Cart icon (Woo only)
            if ( class_exists( 'WooCommerce' ) ) {
                get_template_part( 'template-parts/header/cart-icon' );
            }
            ?>
        </div>

        <!-- Mobile menu trigger -->
        <button class="site-header__mobile-toggle" aria-label="<?php esc_attr_e( 'Open menu', 'yourtheme' ); ?>">
            <span></span>
            <span></span>
            <span></span>
        </button>

    </div>

    <!-- Mobile navigation -->
    <?php get_template_part( 'template-parts/header/mobile-nav' ); ?>

</header>

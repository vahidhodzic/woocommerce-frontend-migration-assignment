
```tree
├── assets/
│   ├── css/
│   │   └── woocommerce.css              # Product page styles
│   └── js/
│       └── product.js                   # cart enhancements
│
├── inc/
│   ├── setup/
│   │   ├── enqueue.php                  # Conditional asset loading
│   │   ├── theme-setup.php              # Loads all inc files
│   │   └── woocommerce-support.php      # WC theme support features
│   └── hooks/
│       └── woocommerce-hooks.php        # All product page logic
│
├── template-parts/
│   └── product/
│       └── product-summary.php          # Triggers woocommerce_single_product_summary
│
├── woocommerce/
│   └── single-product/
│       ├── content-single-product.php   # Main layout
│       ├── single-product.php           # Product page template
│       └── add-to-cart/
│           ├── simple.php               # Simple product form
│           ├── variable.php             # Variable product form
│           └── price.php                # Custom price display
│
└── functions.php                        # Theme loader

```

Example single-product.php override or partials
/woocommerce/content-single-product.php

```
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

```

Usage of WooCommerce hooks
/inc/hooks/woocommerce-hooks.php

```
<?php
/**
 * WooCommerce Single Product Hooks
 * @package Forga
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Remove WooCommerce defaults
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 15 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

// Custom hooks with priority order
add_action( 'woocommerce_single_product_summary', 'forga_product_title', 5 );
function forga_product_title() {
    global $product;
    echo '<h1 class="product_title">' . esc_html( $product->get_name() );
    if ( $product->is_on_sale() ) {
        echo ' <span class="sale-badge">Sale</span>';
    }
    echo '</h1>';
}

add_action( 'woocommerce_single_product_summary', 'forga_product_price', 10 );
function forga_product_price() {
    wc_get_template_part( 'single-product/price' );
}

add_action( 'woocommerce_single_product_summary', 'forga_product_short_description', 20 );
function forga_product_short_description() {
    woocommerce_template_single_excerpt();
}

add_action( 'woocommerce_single_product_summary', 'forga_product_add_to_cart', 30 );
function forga_product_add_to_cart() {
    global $product;
    if ( ! $product->is_purchasable() || ! $product->is_in_stock() ) {
        return;
    }
    $add_to_cart_path = $product->is_type( 'simple' )
        ? 'single-product/add-to-cart/simple'
        : 'single-product/add-to-cart/variable';
    wc_get_template_part( $add_to_cart_path );
}

add_action( 'woocommerce_single_product_summary', 'forga_product_meta', 40 );
function forga_product_meta() {
    woocommerce_template_single_meta();
}

// Gallery wrapper
add_action( 'woocommerce_before_single_product_summary', 'forga_gallery_wrapper', 5 );
function forga_gallery_wrapper() {
    echo '<div class="product-gallery-wrapper">';
    woocommerce_show_product_images();
    echo '</div>';
}
```

Add-to-cart handling for simple and variable products
/woocommerce/single-product/add-to-cart/simple.php

```
<?php
/**
 * Simple Product Add to Cart
 */
global $product;
if ( $product && $product->is_type( 'simple' ) ) {
    woocommerce_simple_add_to_cart();
}
?>



```
/woocommerce/single-product/add-to-cart/variable.php

```
<?php
/**
 * Variable Product Add to Cart
 */
global $product;
if ( $product && $product->is_type( 'variable' ) ) {
    woocommerce_variable_add_to_cart();
}
?>


```

Dynamic Detection Logic:

```
// In woocommerce-hooks.php
function forga_product_add_to_cart() {
    global $product;
    if ( ! $product->is_purchasable() || ! $product->is_in_stock() ) {
        return;
    }
    $add_to_cart_path = $product->is_type( 'simple' )
        ? 'single-product/add-to-cart/simple'
        : 'single-product/add-to-cart/variable';
    wc_get_template_part( $add_to_cart_path );
}

```
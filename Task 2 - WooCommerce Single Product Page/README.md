
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
/woocommerce/single-product/content-single-product.php

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
// Remove WooCommerce defaults
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );

// Custom hooks (priority = visual order)
add_action( 'woocommerce_single_product_summary', 'forga_product_title', 5 );
function forga_product_title() {
    global $product;
    $title = $product->get_name();
    if ( $product->is_on_sale() ) $title .= ' <span class="sale-badge">Sale</span>';
    printf( '<h1 class="product_title">%s</h1>', esc_html( $title ) );
}

add_action( 'woocommerce_single_product_summary', 'forga_product_price', 10 );
function forga_product_price() {
    wc_get_template_part( 'single-product/add-to-cart/price' );
}

add_action( 'woocommerce_single_product_summary', 'forga_product_add_to_cart', 30 );
function forga_product_add_to_cart() {
    global $product;
    $template = $product->is_type( 'simple' )
        ? 'single-product/add-to-cart/simple'
        : 'single-product/add-to-cart/variable';
    wc_get_template_part( $template );
}

// Gallery wrapper
add_action( 'woocommerce_before_single_product_summary', 'forga_gallery_wrapper', 5 );
function forga_gallery_wrapper() {
    echo '<div class="product-gallery-wrapper">';
    woocommerce_show_product_images();
    echo '</div>';
}
?>
```

Add-to-cart handling for simple and variable products
/woocommerce/single-product/add-to-cart/simple.php

```
<?php
/**
 * Simple product add-to-cart form
 */
global $product;
if ( $product->is_purchasable() && $product->is_in_stock() ) :
    woocommerce_simple_add_to_cart(); // Quantity + AJAX button
endif;
?>


```
/woocommerce/single-product/add-to-cart/variable.php

```
<?php
/**
 * Variable product add-to-cart form
 */
global $product;
woocommerce_variable_add_to_cart(); // Dropdowns + validation + AJAX
?>

```

Dynamic Detection Logic:

```
// In woocommerce-hooks.php
$template = $product->is_type( 'simple' )
    ? 'single-product/add-to-cart/simple'
    : 'single-product/add-to-cart/variable';
wc_get_template_part( $template );

```
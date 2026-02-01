# Forga Theme – WooCommerce Custom Theme Architecture

This document explains the structure and responsibilities of the **Forga-Theme** WordPress theme, designed for a WooCommerce store. The focus is on clear separation of concerns, maintainability, and compatibility with WooCommerce updates.

Someone else touches the theme later

You revisit it in 6–12 months

You hand it off to another dev


├── assets/
│   ├── css/
│   │   ├── main.css
│   │   └── woocommerce.css
│   ├── js/
│   │   ├── main.js
│   │   └── product.js
│   └── images/
│
├── inc/
│   ├── setup/
│   │   ├── theme-setup.php
│   │   ├── enqueue.php
│   │   └── woocommerce-support.php
│   │
│   ├── helpers/
│   │   ├── formatting.php
│   │   └── utilities.php
│   │
│   ├── hooks/
│   │   ├── global-hooks.php
│   │   └── woocommerce-hooks.php
│   │
│   └── woocommerce/
│       ├── product-functions.php
│       ├── cart-functions.php
│       └── checkout-functions.php
│
├── woocommerce/
│   ├── single-product.php
│   ├── content-single-product.php
│   └── single-product/
│       ├── price.php
│       ├── add-to-cart/
│       │   ├── simple.php
│       │   └── variable.php
│
├── template-parts/
│   ├── header/
│   ├── footer/
│   └── product/
│       └── product-summary.php
│
├── functions.php
├── style.css
├── index.php
├── header.php
├── footer.php
├── page.php
├── single.php
├── archive.php
└── README.md

## 1. High-level structure

- `assets/` – All static assets (CSS, JS, images).
- `inc/` – PHP logic: setup, helpers, hooks, and WooCommerce-specific functionality.
- `woocommerce/` – WooCommerce template overrides.
- `template-parts/` – Reusable partial templates.
- Root templates (`page.php`, `single.php`, `archive.php`, `index.php`, header.php, footer.php) – Core WordPress template hierarchy.
- `functions.php` – Theme bootstrap.
- `style.css` – Theme stylesheet and header metadata.
- `README.md` – This documentation.

---

## 2. Assets

### 2.1 CSS

- `assets/css/main.css`
  Global site styling: layout, typography, buttons, navigation, and shared components.

- `assets/css/woocommerce.css`
  Styles specific to WooCommerce pages: product grids, single product layouts, cart, checkout, notices.

### 2.2 JavaScript

- `assets/js/main.js`
  Global frontend behavior such as navigation toggles, modals, and generic UI interactions.

- `assets/js/product.js`
  Single product page behavior: galleries, variation handling (e.g. UI), accordions, and other product-specific UX.

### 2.3 Images

- `assets/images/`
  Theme images such as logos, icons, and placeholders used across templates.

---

## 3. Theme logic (`inc/`)

The `inc/` directory contains all PHP logic, split by responsibilities to keep templates thin and maintainable.

### 3.1 Setup (`inc/setup/`)

- `theme-setup.php`
  - Registers theme supports (title-tag, post-thumbnails, HTML5, etc.).
  - Registers navigation menus and sidebars.
  - Configures image sizes and other core theme options.

- `enqueue.php`
  - Registers and enqueues frontend CSS/JS.
  - Hooks into `wp_enqueue_scripts` (and admin equivalents if needed).
  - Loads `main.css`, `woocommerce.css`, `main.js`, and `product.js` with proper dependencies and conditions (e.g. only load `product.js` on product pages).

- `woocommerce-support.php`
  - Adds `add_theme_support( 'woocommerce' )`.
  - Configures WooCommerce image sizes and gallery features (zoom, lightbox, slider).
  - Any WooCommerce-specific theme support declarations.

### 3.2 Helpers (`inc/helpers/`)

- `formatting.php`
  - Helper functions for formatting: prices, labels, badges, text snippets.
  - Reusable presentation logic consumed by templates and hooks.

- `utilities.php`
  - Generic utility functions (sanitization wrappers, small reusable helpers) used across theme files.

### 3.3 Hooks (`inc/hooks/`)

- `global-hooks.php`
  - Registers and defines hooks and filters that affect general WordPress output (header, footer, main loops, etc.).
  - Contains any layout or content tweaks not specific to WooCommerce.

- `woocommerce-hooks.php`
  - Registers WooCommerce-specific actions and filters, e.g. for single product, shop loop, cart, and checkout.
  - Reorders elements on the single product page, adds/removes default WooCommerce template actions, and injects custom partials.
  - Calls functions defined in `inc/woocommerce/*.php`.

### 3.4 WooCommerce logic (`inc/woocommerce/`)

- `product-functions.php`
  - Custom product-related logic such as dynamic badges, stock messages, extra data blocks, and product meta presentation.
  - Helper functions used by hooks on single product and product loop templates.

- `cart-functions.php`
  - Cart-related behavior: custom notices, mini-cart adjustments, cart totals formatting, etc.
  - Hooks for cart fragments and cart page UX.

- `checkout-functions.php`
  - Checkout fields configuration (add/remove/reorder fields).
  - Validation logic and custom messages.
  - Hooks for improving checkout UX and adding custom sections.

---

## 4. WooCommerce templates (`woocommerce/`)

These are **update-safe** overrides that follow WooCommerce’s template structure. They change layout/markup where hooks alone are not sufficient.

- `woocommerce/single-product.php`
  - The main layout file for the single product page.
  - Provides the overall HTML structure and calls core WooCommerce hooks such as:
    - `woocommerce_before_single_product`
    - `woocommerce_before_single_product_summary`
    - `woocommerce_single_product_summary`
    - `woocommerce_after_single_product_summary`
    - `woocommerce_after_single_product`
  - Keeps these hooks intact to remain compatible with WooCommerce updates.

- `woocommerce/content-single-product.php`
  - The main content template for a single product, typically included by `single-product.php`.
  - Uses WooCommerce hooks to render title, price, excerpt, add-to-cart, meta, and related products.
  - Minimal custom logic; relies on functions defined in `inc/woocommerce/` and hooks in `inc/hooks/woocommerce-hooks.php`.

- `woocommerce/single-product/price.php`
  - Override for the price block to adjust markup, position, or additional labels around the price.

- `woocommerce/single-product/add-to-cart/simple.php`
  - Override for the simple product add-to-cart area.
  - Maintains WooCommerce’s add-to-cart behavior while allowing custom markup or wrapper elements.

- `woocommerce/single-product/add-to-cart/variable.php`
  - Override for variable product add-to-cart.
  - Customizes the layout around variation selectors and add-to-cart button, while leaving core logic intact.

---

## 5. Template parts (`template-parts/`)

Reusable partials that can be loaded with `get_template_part()` or via hooks.

- `template-parts/header/`
  - Header partials (e.g. main header, mobile header, minimal header) used by `header.php` or directly from hooks.

- `template-parts/footer/`
  - Footer partials (e.g. default footer, shop footer) used by `footer.php` or hooks.

- `template-parts/product/product-summary.php`
  - A custom product summary block that can be inserted into the single product page via WooCommerce hooks.
  - Keeps detailed product layout separate from core templates and functions.

---

## 6. Root templates and core theme files

- `functions.php`
  - Theme bootstrap file.
  - Requires/loads files from `inc/` (setup, helpers, hooks, WooCommerce logic).
  - Registers global hooks and initial configuration.

- `style.css`
  - Contains the theme header comment (name, author, version, etc.) required by WordPress.
  - Can import or reference compiled CSS if needed.

- `header.php`
  - Defines the document-level HTML structure (<!doctype>, <html>, <head>, <body>).
  - Outputs required WordPress hooks such as wp_head() and wp_body_open().
  - Acts as the entry point for the site header, loading header layout partials from template-parts/header/.
  - Can conditionally load different header variants (e.g. default, product, cart, checkout).

- `footer.php`
  - Closes the document structure and outputs global footer markup.
  - Acts as the entry point for the site footer, loading footer layout partials from template-parts/footer/.
  - Outputs the wp_footer() hook required by WordPress, plugins, and WooCommerce.
  - Can conditionally load simplified footers for special contexts (e.g. checkout).

- `index.php`
  - Fallback template for all requests when no more specific template exists in the hierarchy.

- `page.php`
  - Template for static pages (e.g. About, Contact, landing pages).
  - Uses standard WordPress loop and can include header/footer and partials.

- `single.php`
  - Template for single posts (blog posts).
  - Provides blog post layout separate from WooCommerce product templates.

- `archive.php`
  - Template for archive views (categories, tags, date archives, custom post type archives).
  - Handles listing of posts with pagination, can be customized for blog archives.

- `README.md`
  - Documentation of the theme’s architecture, file responsibilities, and key implementation decisions.

---

## 7. WooCommerce best practices and compatibility

- WooCommerce-specific logic is placed in `inc/woocommerce/` and wired via hooks in `inc/hooks/woocommerce-hooks.php`, avoiding heavy logic inside template files.
- Template overrides in `woocommerce/` are kept as small and focused as possible, preserving standard WooCommerce hooks and core behavior.
- Add-to-cart handling for simple and variable products primarily leverages WooCommerce’s default mechanisms, with layout and UX enhancements added via hooks and minimal template overrides.
- This structure minimizes maintenance overhead and reduces the risk of breakage when WooCommerce updates its internal templates or logic.

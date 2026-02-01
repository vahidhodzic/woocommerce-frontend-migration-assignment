# Headless to Classic WordPress Migration Guide

This guide demonstrates a safe migration from a headless WordPress setup back to a traditional frontend, with a focus on WooCommerce-heavy sites. It preserves all critical data (products, orders, users, SEO) while ensuring high-traffic resilience.

## 1. Cloning Production Safely

### Step 1: Staging Environment
- Create an isolated staging environment on the same hosting cluster to minimize latency for high-volume traffic testing.
- **Protect staging**:
  - Password-protect the site.
  - Block search engines via `robots.txt`.
- **Clone components**:
  - Full database.
  - `wp-content/uploads`.
  - Active plugins and themes.

### Step 2: Infrastructure-Level Cloning
For high-traffic sites, leverage host-provided tools:
- WP Engine, Kinsta, or Cloudways staging environments (optimized for WooCommerce).
- **Manual alternatives**:
  - `mysqldump` for database.
  - `rsync` for `wp-content`.
- **Verify staging matches production**:
  - Identical PHP & MySQL versions.
  - Matching object cache (Redis/Memcached).

### Step 3: Freeze Writes
During final migration:
- Enable maintenance mode.
- Temporarily stop new orders and user registrations.
- Capture a final database snapshot for zero data loss.

## 2. Headless Plugins & Services to Remove/Disable

### Core Headless Plugins
- WPGraphQL & WPGraphQL WooCommerce.
- Headless rendering/JSON endpoint plugins.
- Frontend-only API handlers (e.g., custom React/Vue bridges).

### CDN / Edge Functions
- Temporarily disable:
  - Vercel/Netlify redirects.
  - Cloudflare Workers modifying API endpoints.
- **Keep active**: Standard caching layers (CDN for images, JS, CSS).

### Theme Switch
- Replace headless theme with a classic WooCommerce-compatible PHP theme.
- Retain `functions.php` customizations.
- Ensure WooCommerce templates exist:
  - `single-product.php`
  - `archive-product.php`
  - Checkout templates.

> ⚠️ **Never delete** WooCommerce core or data-handling plugins.

## 3. Preserving Critical WooCommerce & SEO Data

| Data Type | Validation Points | Storage Location |
|-----------|------------------|------------------|
| **Products** | SKUs, prices, variations, stock levels | `wp_posts` + `wp_postmeta`, `wp_woocommerce_*` tables |
| **Orders** | Order IDs, statuses, notes, payment history | `wp_woocommerce_order_*` tables |
| **Users** | Roles, passwords, meta, order history | `wp_users` + `wp_usermeta` |
| **SEO** | Yoast/RankMath metadata, permalinks, canonicals, OpenGraph tags | `wp_postmeta`, `wp_options` |

- **No re-imports needed**—data remains untouched.
- Disable staging webhooks to prevent fulfillment triggers.
- Test login, account pages, and order creation on staging first.
- **SEO safety**: No URL changes = no ranking impact.

## 4. Migration Checklist (WooCommerce Focus)

### Pre-Migration
- [ ] Full database + `wp-content` backup (2+ copies).
- [ ] Document plugin/theme versions and custom hooks.
- [ ] Crawl site for SEO/product URL baseline.
- [ ] Enable maintenance mode.

### Staging Validation
- [ ] Product pages render correctly.
- [ ] Cart, checkout, payment gateways functional.
- [ ] Orders create successfully.
- [ ] Customer account pages work.
- [ ] SEO metadata preserved.
- [ ] Performance/load testing (peak traffic simulation).

### Production Switch
- [ ] Deploy classic theme & plugin changes.
- [ ] Flush permalinks.
- [ ] Clear object cache & CDN cache.
- [ ] Disable maintenance mode.

### Post-Launch
- [ ] Test order creation/checkout.
- [ ] Verify emails & payment responses.
- [ ] Confirm real-time stock updates.
- [ ] Crawl site for SEO verification.
- [ ] Monitor error logs (24-48 hours).

## 5. Rollback Strategy (High-Traffic Safety)

### Immediate Rollback (<5 Minutes)
1. Restore final DB snapshot + `wp-content`.
2. Re-enable headless theme & API endpoints.
3. **No DNS changes** = instant effect.

### Partial Rollback
- Switch back to headless frontend only.
- Keep WooCommerce database intact.
- Resume order processing seamlessly.

### SEO Rollback Safety
- URLs unchanged.
- Meta tags preserved.
- No redirects modified.
- Minimal ranking impact.

## Migration Flow Diagram
```
┌──────────────────────┐
│ Production (Headless)│
│ - WPGraphQL/REST │
│ - React/Vue Frontend │
│ - WooCommerce Core │
└─────────┬───────────┘
│
│ Clone DB + wp-content
▼
┌──────────────────────┐
│ Staging Environment │
│ - Classic Theme Test │
│ - Checkout Simulation│
│ - SEO Verification │
└─────────┬───────────┘
│
│ Validation: Products/Stock,
│ Orders/Users, SEO/Permalinks,
│ Peak Load Test
▼
┌──────────────────────┐
│ Maintenance Mode On │
│ - Freeze Orders │
│ - Prevent Registrations
└─────────┬───────────┘
│
│ Deploy Classic Theme
│ Disable Headless Plugins
▼
┌──────────────────────┐
│ Production (Classic) │
│ - WooCommerce Pages │
│ - Checkout & Orders │
│ - SEO Meta Preserved │
│ - CDN & Caching │
└─────────┬───────────┘
│
┌─────────┴───────────┐
│ Post-Launch Testing │
│ - Test Orders │
│ - Stock Updates │
│ - SEO Crawl │
│ - Monitor Logs │
└─────────┬───────────┘
│
┌─────────┴───────────┐
│ Rollback Ready │
│ - Restore Headless │
│ - Restore DB Snapshot│
│ - Resume Checkout │
└─────────────────────┘
```
## Key Principles
- **Data-first**: Orders, products, users, stock never touched.
- **Frontend swap-only**: Changes limited to presentation layer.
- **Test rigorously**: High-traffic simulation on staging.
- **Rollback-ready**: Instant fallback preserves revenue.

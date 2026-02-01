# 1. SOLUTION - ACF Toggle

# SETUP
1. Install ACF Free
(no need to add the field in acf dashboard, we are adding though the code, we just need acf to be installed,
if we want we can remove the field from the code and add it though acf following this instruction)
Custom Fields → Add New → "Category Visibility"
Location: Taxonomy = product_cat
Field: True/False, Name="hide_in_listings", Styling+Toggle
2. Copy ACF code to functions.php

#   Features
Products → Categories → Toggle switch appears
Hide from: Shop page, menus, menu builder, admin menus
Direct URLs /product-category/secret/ → Products show normally
Custom links in menus → Auto-removed from frontend
Zero performance impact (cached queries)


# 2. SOLUTION META (Native Wordpress)

# SETUP
1. Copy Native code to functions.php
2. Products → Categories → "Hide in Listings" checkbox appears

#   Features
Native checkbox (no ACF needed)
Identical functionality to ACF version
All hiding locations covered
Direct URLs work perfectly
Custom menu links blocked

# 3. SOLUTION Admin Setting Page

# SETUP
1. Copy Hybrid code to functions.php
2. Products → Hidden Categories (new admin page)
3. Enter slugs (one per line):
   secret-sale
   wholesale-only
4. Save → Instantly hidden everywhere

#   Features
Bulk management - textarea input
No per-category editing needed
Perfect for agencies/marketing teams
Visual preview + count verification

---------------------------------------------------------

# Hooks Used

pre_get_posts          // Shop page
wp_get_nav_menu_items  // Frontend menus + custom links
wp_nav_menu_terms_checklist_args  // Menu builder
get_terms              // Admin menus screen

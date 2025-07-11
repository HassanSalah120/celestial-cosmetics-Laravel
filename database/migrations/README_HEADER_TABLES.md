# Header Tables Migration History

## Overview

The header navigation system in Celestial Cosmetics was initially built using two separate tables:

1. `header_navigation_items` - For storing navigation menu items
2. `header_settings` - For storing header configuration settings as key-value pairs

As part of a refactoring effort, these tables were consolidated to improve performance and maintainability:

- The `header_settings` table was replaced with a new `global_header_settings` table that uses columns instead of key-value pairs
- Additional fields were added to the `header_navigation_items` table for better control over menu items
- The old `header_settings` table was backed up and then dropped

## Migration Timeline

1. Original migrations (2025-06-20):
   - `2025_06_20_000000_create_header_navigation_items_table` - Created the initial navigation items table
   - `2025_06_20_000001_update_header_settings_table` - Created the initial header settings table
   - `2025_06_20_000002_add_timestamps_to_header_settings` - Added timestamps to the settings table

2. Consolidation migrations (2025-06-19):
   - `2025_06_19_001120_consolidate_header_tables` - Created the new global_header_settings table and added fields to header_navigation_items
   - `2025_06_19_002126_drop_header_settings_table` - Backed up and dropped the old header_settings table
   - `2025_06_19_004000_drop_header_settings_backup_table` - Backed up and dropped the header_settings_backup table

3. Documentation migration:
   - `2025_06_19_004129_create_header_tables_consolidated` - Consolidated migration for documentation purposes

## Current Structure

### global_header_settings

This table stores global settings for the header:

- `show_logo` - Whether to show the site logo in the header
- `show_profile` - Whether to show the user profile link in the header
- `show_store_hours` - Whether to show store hours in the header
- `show_search` - Whether to show the search box in the header
- `show_cart` - Whether to show the shopping cart in the header
- `show_language_switcher` - Whether to show the language switcher in the header
- `show_auth_links` - Whether to show login/register links for guests
- `sticky_header` - Whether to make the header sticky when scrolling
- `header_style` - The style of the header (default, centered, minimal, full-width)
- `logo` - The path to the header logo image

### header_navigation_items

This table stores the navigation menu items:

- `parent_id` - The ID of the parent menu item (for dropdowns)
- `name` - The name of the menu item in English
- `name_ar` - The name of the menu item in Arabic
- `route` - The Laravel route name for the menu item
- `url` - The URL for the menu item (if no route is specified)
- `translation_key` - The translation key for the menu item
- `open_in_new_tab` - Whether to open the link in a new tab
- `sort_order` - The order of the menu item
- `is_active` - Whether the menu item is active
- `has_dropdown` - Whether the menu item has a dropdown
- `show_in_header` - Whether to show the item in the header
- `show_in_footer` - Whether to show the item in the footer
- `show_in_mobile` - Whether to show the item in the mobile menu
- `icon` - The icon for the menu item
- `badge_text` - The text for a badge on the menu item
- `badge_color` - The color for the badge 
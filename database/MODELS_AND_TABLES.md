# Database Models and Tables Documentation

## Overview
This document tracks the relationship between database tables and Eloquent models in the Celestial Cosmetics application. It identifies tables without corresponding models and provides guidance for future development.

## Tables Without Models

The following tables exist in the database schema but don't have corresponding Eloquent models:

1. **AboutPage** ✅ Model created
2. **`color_scheme`** - Contains color scheme settings (may have been superseded by settings table)
3. **`consolidated_navigation`** - Navigation data (may be redundant with other navigation tables)
4. **`contact_page`** - Stores content for the Contact page
5. **`corporate_values`** ✅ Model created
6. **`dashboard_cards`** - Dashboard UI components configuration
7. **`dashboard_visibility`** - Controls visibility of dashboard elements
8. **`homepage_hero`** - Stores homepage hero section content
9. **`homepage_sections`** - Defines sections displayed on the homepage
10. **`homepage_settings`** - Contains settings for homepage layout and display
11. **`navigation_items`** - Stores navigation menu items (may be redundant with header_navigation_items)
12. **`our_story_content`** - Content for the Our Story section
13. **`page_seo`** - SEO metadata for specific pages
14. **`product_card_settings`** - Display settings for product cards
15. **`product_display_settings`** - General product display configuration
16. **`store_hours`** - Business hours information
17. **TeamMember** ✅ Model created

These tables may be accessed through direct database queries or through service classes rather than Eloquent models. Consider creating models for frequently accessed tables to maintain consistency with the application's architecture.

## Recently Added Models

As part of the migration and model optimization process, the following models were created for tables that previously used direct DB queries:

1. **AboutPage** (`app/Models/AboutPage.php`)
   - Represents the `about_page` table
   - Contains content for the About Us page
   - Previously accessed via direct DB queries in AboutController

2. **CorporateValue** (`app/Models/CorporateValue.php`)
   - Represents the `corporate_values` table
   - Contains company values shown on the About page
   - Previously accessed via direct DB queries in AboutController

3. **TeamMember** (`app/Models/TeamMember.php`)
   - Represents the `team_members` table
   - Contains team member information displayed on the About page
   - Previously accessed via direct DB queries in AboutController

## Models without Tables (Pending Migrations)

The `NewsletterSubscription` model exists but its table is not yet created in the current schema. There is a pending migration that would create this table. The model is actively used in the codebase for newsletter functionality.

## Implementation Notes

For controllers currently using direct DB queries to access tables without models:
1. Update to use the appropriate Eloquent model instead
2. Use relationships where appropriate to connect related models
3. Take advantage of Eloquent's query building capabilities

## Next Steps

Models should be created for the remaining tables that are actively used in the codebase:
1. HomepageHero
2. HomepageSection
3. HomepageSetting
4. DashboardVisibility
5. OurStoryContent

These models will facilitate better code organization, improve type safety, and allow for more robust relationships between entities.

## Database Schema Synchronization

The application uses a schema synchronization approach where:

1. A master schema file (`database/current_schema.sql`) contains the complete database structure
2. The migration `2025_04_07_022208_sync_database_with_actual_schema.php` serves as a marker for when the schema was synchronized
3. For new installations, it's recommended to import the schema directly rather than running all migrations

## Removed Redundant Elements

As part of the cleanup, the following redundant elements were removed:

### Duplicate Migrations:
- `2023_05_15_144510_create_newsletter_subscriptions_table.php` (kept `2025_04_07_031315_create_newsletter_subscriptions_table.php`)
- `2024_03_27_create_legal_pages_table.php` (kept `2024_07_07_000000_create_legal_pages_table.php`)
- `2024_03_18_000001_create_normalized_settings_tables.php` and `2024_03_18_000002_create_more_normalized_settings_tables.php` (kept `2025_04_06_000001_create_normalized_settings_tables.php`)
- `2025_03_26_012221_create_dashboard_widgets_table.php` (table is explicitly dropped in `2025_04_07_015643_remove_unused_tables.php`)

### Obsolete Models:
- `DashboardWidget.php` - Table was explicitly dropped in `2025_04_07_015643_remove_unused_tables.php` 
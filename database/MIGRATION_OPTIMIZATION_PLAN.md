# Migration and Model Optimization Plan

## Current Status

The project currently has 95+ migrations with many incremental changes to the same tables. We'll consolidate these into single migrations per table to simplify the database schema management.

## Tables with Models (Keep)

These tables have corresponding models and will be retained with consolidated migrations:

1. `activities` → `Activity.php`
2. `categories` → `Category.php` 
3. `contact_messages` → `ContactMessage.php`
4. `country_shipping_fees` → `CountryShippingFee.php`
5. `coupon_usages` → `CouponUsage.php`
6. `coupons` → `Coupon.php`
7. `currency_config` → `CurrencyConfig.php`
8. `email_logs` → `EmailLog.php`
9. `email_templates` → `EmailTemplate.php`
10. `footer_links` → `FooterLink.php`
11. `footer_sections` → `FooterSection.php`
12. `footer_settings` → `FooterSetting.php`
13. `general_settings` → `GeneralSetting.php`
14. `header_navigation_items` → `HeaderNavigationItem.php`
15. `header_settings` → `HeaderSetting.php`
16. `inventory_transactions` → `InventoryTransaction.php`
17. `offers` → `Offer.php`
18. `order_items` → `OrderItem.php`
19. `orders` → `Order.php`
20. `payment_configs` → `PaymentConfig.php`
21. `product_images` → `ProductImage.php`
22. `products` → `Product.php`
23. `redirects` → `Redirect.php`
24. `reviews` → `Review.php`
25. `robots_txt_rules` → `RobotsTxtRule.php`
26. `schema_markups` → `SchemaMarkup.php`
27. `seo_defaults` → `SeoDefaults.php`
28. `setting_translations` → `SettingTranslation.php`
29. `settings` → `Setting.php`
30. `shipping_config` → `ShippingConfig.php`
31. `shipping_methods` → `ShippingMethod.php`
32. `structured_data` → `StructuredData.php`
33. `testimonials` → `Testimonial.php`
34. `users` → `User.php`

## Tables without Models (Used via DB Queries)

These tables exist in the schema and are accessed via direct DB queries in the codebase. They should be kept, and Eloquent models should be created for them:

1. `about_page` - Used in AboutController and AboutPageController
2. `corporate_values` - Used in AboutController and AboutPageController
3. `dashboard_visibility` - Used in UserController
4. `homepage_hero` - Used in HomeController and HomepageController
5. `homepage_sections` - Used in HomeController and HomepageController
6. `homepage_settings` - Used in multiple controllers and Product model
7. `our_story_content` - Used in HomeController and HomepageController
8. `team_members` - Used in AboutController, AboutPageController, and CleanupTeamMemberImages command

## Tables without Models (No Clear Usage)

These tables exist in the schema but have no clear evidence of usage in the codebase. Further investigation is needed before deciding to keep or remove them:

1. `color_scheme`
2. `consolidated_navigation`
3. `contact_page`
4. `dashboard_cards`
5. `navigation_items`
6. `page_seo`
7. `product_card_settings`
8. `product_display_settings`
9. `store_hours`

## Framework Tables (Keep)

These tables are used by Laravel and should be retained:

1. `cache`
2. `cache_locks`
3. `failed_jobs`
4. `job_batches`
5. `jobs`
6. `migrations`
7. `password_reset_tokens`
8. `personal_access_tokens`
9. `sessions`

## Models without Tables (Pending Migration)

The `NewsletterSubscription` model exists but its table is not yet created in the current schema. There is a pending migration `2025_04_07_031315_create_newsletter_subscriptions_table.php` that would create this table. The model is actively used in the codebase for newsletter functionality.

This indicates the application is in a transitional state where some planned database changes haven't been applied yet. We should execute the pending migrations before proceeding with optimization.

## Optimization Strategy

1. **Create consolidated migrations**: For each table to keep, create a single migration that establishes the complete table structure.

2. **Remove all existing migrations**: Once the consolidated migrations are created and tested, remove all existing migrations.

3. **Update migration documentation**: Update the documentation to reflect the new consolidated approach.

4. **Create a database seed**: Develop a comprehensive database seeder to populate essential data.

## Implementation Plan

1. **Backup the current database**: Ensure a complete backup exists before making any changes.

2. **Generate schema snapshots**: For each table to keep, generate a SQL schema definition from the current database.

3. **Create consolidated migrations**: Develop new migrations using the schema snapshots.
   - ✅ Example migrations created:
     - `database/migrations/consolidated/2024_01_01_000001_create_consolidated_users_table.php`
     - `database/migrations/consolidated/2024_01_01_000002_create_consolidated_products_table.php`
     - `database/migrations/consolidated/2024_01_01_000003_create_consolidated_orders_table.php`

4. **Create models for tables without models**:
   - ✅ Models created:
     - `app/Models/AboutPage.php`
     - `app/Models/CorporateValue.php`
     - `app/Models/TeamMember.php`

5. **Test the consolidated migrations**: Verify that the new migrations create an identical schema.

6. **Remove old migrations**: Once testing confirms success, remove the old migrations.

7. **Update documentation**: Update all documentation to reflect the new approach.
   - ✅ Created detailed implementation plan: `database/MIGRATION_CONSOLIDATION.md`

## Risks and Mitigation

- **Data loss**: Backup thoroughly before beginning.
- **Schema mismatch**: Test thoroughly on a staging environment.
- **Table dependency issues**: Create migrations in the correct order to handle foreign key dependencies.

## Additional Recommendations

- Consider adding Eloquent models for frequently accessed tables that currently lack models.
- Document any tables that are accessed via direct SQL queries rather than through models.
- Add proper foreign key constraints where they may be missing. 
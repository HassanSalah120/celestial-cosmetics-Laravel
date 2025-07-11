# Database Schema Synchronization

## Problem Statement
The database schema was initially created using migrations, but manual changes were made directly to the database structure through a database tool. These changes were not recorded in migration files, resulting in a discrepancy between the defined migrations and the actual database structure.

## Solution Applied
1. A complete SQL dump of the current database schema (without data) has been saved to `database/current_schema.sql`.
2. A marker migration (`2025_04_07_022208_sync_database_with_actual_schema.php`) has been created to indicate that the schema has been synchronized.
3. This approach provides a clean reference point for future migrations.

## For Fresh Installations
When setting up this project on a new environment, follow these steps:

1. Create a new database
2. Import the schema from `database/current_schema.sql`:
   ```bash
   mysql -u username -p database_name < database/current_schema.sql
   ```
3. Run any migrations that were created after the synchronization:
   ```bash
   php artisan migrate --path=database/migrations/2025_04_07_022208_sync_database_with_actual_schema.php --force
   php artisan migrate
   ```

## For Adding New Migrations
Continue creating migrations as usual. New migrations will build upon the synchronized schema.

```bash
php artisan make:migration add_new_feature
```

## Important Notes
- Do not run `php artisan migrate:fresh` as it will attempt to run all migrations from the beginning, which might not match the current schema.
- If you need to reset the database completely, use the SQL dump instead, then run all migrations that came after the synchronization.
- Always create new migrations for any schema changes instead of modifying the database directly.
- Refer to `MODELS_AND_TABLES.md` for a list of tables that do not have corresponding Eloquent models.

## Recent Cleanup
- Redundant migrations have been removed to avoid confusion:
  - Duplicate newsletter subscription table migrations
  - Duplicate legal pages table migrations
  - Multiple normalized settings tables migrations
- The obsolete `DashboardWidget` model has been removed since its table was dropped in the migration `2025_04_07_015643_remove_unused_tables.php`.
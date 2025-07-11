# Laravel Migration Cleanup

This project has undergone a migration cleanup process. The following actions were taken:

1. All migrations were analyzed and grouped by table
2. Redundant or incremental migrations were merged into single clean migrations per table
3. The migration order was determined based on table dependencies

## Deployment Instructions

To apply these changes to your database:

1. Make sure you have a backup of your database
2. Run the following command to apply the new migrations:

```
php artisan migrate:fresh
```

**Warning**: This will reset your entire database. If you need to preserve data, you should:

1. Export your data
2. Run `php artisan migrate:fresh`
3. Import your data back

## Backup Information

A backup of the original migrations can be found in: C:\xampp\htdocs\celestial-cosmetics/database/migrations_backup_2025_06_07_064036
A SQL script for direct database updates (if needed) can be found at: C:\xampp\htdocs\celestial-cosmetics/database/migration_cleanup_2025_06_07_064036.sql

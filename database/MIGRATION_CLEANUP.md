# Laravel Migration Cleanup Process

This directory contains utilities to clean up and consolidate Laravel migrations. These tools will help you analyze, merge, and optimize your migration files to improve maintainability and deployment efficiency.

## Available Tools

### 1. migration_analyzer.php
- Scans all migration files in your project
- Groups migrations by table
- Analyzes dependencies between tables
- Generates consolidated migration files

### 2. detect_unused_tables.php
- Connects to your database to get all tables
- Checks if each table has a corresponding model
- Detects if the model is actually used in the codebase
- Identifies tables that can be safely removed

### 3. finalize_migration_cleanup.php
- Creates a backup of your current migrations
- Moves the consolidated migrations to the migrations directory
- Generates a deployment SQL script (if needed)
- Creates documentation for the migration process

### 4. cleanup_migrations.php
- Orchestrates the entire cleanup process
- Runs all tools in the correct sequence
- Provides interactive prompts for user confirmation

## How to Use

1. Make sure you have a backup of your database and migration files
2. Run the main cleanup script:
   ```
   php cleanup_migrations.php
   ```
3. Follow the on-screen instructions and confirm each step
4. Review the generated files in the `database/merged_migrations` directory
5. Apply the changes by running:
   ```
   php artisan migrate:fresh
   ```

## Important Notes

- The migration cleanup process will replace your existing migration files with consolidated ones
- A backup of your original migrations will be created in `database/migrations_backup_TIMESTAMP`
- You should review the consolidated migrations before applying them
- Running `php artisan migrate:fresh` will reset your database, so make sure you have a backup of your data
- If you need to preserve data, you should export it before running the cleanup and import it afterward

## Expected Results

After completing the migration cleanup process:

1. Your migration files will be organized by table
2. Each table will have a single migration file representing its final schema
3. Redundant or obsolete migrations will be removed
4. Unused tables will be identified for potential removal
5. The migration sequence will be optimized based on table dependencies

## Troubleshooting

If you encounter any issues during the cleanup process:

1. Check the backup directory for your original migration files
2. Review the error messages and logs
3. You can run individual tools manually to debug specific issues

For example:
```
php migration_analyzer.php
php detect_unused_tables.php
php finalize_migration_cleanup.php
``` 
# Migration Consolidation Implementation Plan

## Overview

This document provides a detailed plan for consolidating the Laravel migrations in the Celestial Cosmetics project. The goal is to reduce the large number of incremental migrations to a single migration per table, making the database schema more maintainable.

## Prerequisites

Before beginning the migration consolidation process:

1. **Backup your database**: Create a full backup of your production database.
2. **Create a staging environment**: Set up a separate environment to test the migration process.
3. **Run all pending migrations**: Ensure all pending migrations are applied to have the complete schema.

## Step 1: Create a Database Snapshot

First, create a snapshot of the current database schema:

```bash
php artisan db:snapshot
```

This command will generate a SQL dump of the current database schema structure (without data).

## Step 2: Generate Consolidated Migrations

Create a new set of consolidated migrations in a separate directory:

```bash
mkdir -p database/migrations/consolidated
```

For each table in the database, create a single migration that represents its final structure. See the examples in the `database/migrations/consolidated` directory.

Use the following naming convention for the consolidated migrations:
- `2024_01_01_000001_create_consolidated_users_table.php`
- `2024_01_01_000002_create_consolidated_products_table.php`
- etc.

Ensure the migrations are ordered correctly to handle foreign key dependencies.

## Step 3: Create Models for Tables without Models

For tables that are actively used in the codebase but lack Eloquent models:

1. Create appropriate model classes in the `app/Models` directory.
2. Update controllers to use these models instead of direct DB queries.

## Step 4: Test the Consolidated Migrations

Test the consolidated migrations on the staging environment:

1. Delete the existing database schema:
```bash
php artisan migrate:reset
```

2. Delete the migration records:
```bash
php artisan db:wipe
```

3. Copy the consolidated migrations to the main migrations directory:
```bash
cp database/migrations/consolidated/* database/migrations/
```

4. Run the consolidated migrations:
```bash
php artisan migrate
```

5. Verify the schema structure matches the original:
```bash
php artisan db:compare-snapshot
```

## Step 5: Import Test Data

Import a subset of test data to verify that the application functions correctly with the new schema.

## Step 6: Replace Migrations in Production

Once the consolidated migrations have been thoroughly tested:

1. **Backup the production database**
2. Create a deployment plan that includes:
   - Backing up the existing migrations folder
   - Replacing it with the consolidated migrations
   - Running `php artisan migrate:fresh` on the production database (after backup)
   - Importing the production data back

## Step 7: Update Documentation

Update the project documentation to reflect the new migration structure:

1. Document the migration consolidation process
2. Update any references to specific migrations
3. Update the database schema documentation

## Risks and Mitigations

### Data Loss Risk
- **Risk**: Complete data loss if `migrate:fresh` is run without proper backup
- **Mitigation**: Create multiple backups before any operation, including structure and data

### Schema Mismatch Risk
- **Risk**: Consolidated migrations might not exactly match the current schema
- **Mitigation**: Use comparison tools to verify schema equality before and after

### Deployment Timing Risk
- **Risk**: Long downtime during deployment
- **Mitigation**: Perform the migration during scheduled maintenance windows

## Conclusion

This consolidated migration approach will significantly reduce the complexity of the database schema management. By reducing dozens of migrations to a single migration per table, future developers will have a clearer understanding of the database structure and can more easily make changes to it.

## Example Commands

Here's a script that demonstrates the full process:

```bash
#!/bin/bash
# Migration consolidation script

# Backup the database
php artisan backup:run

# Create a schema snapshot
php artisan schema:dump --prune

# Reset the database in the staging environment
php artisan migrate:reset
php artisan db:wipe

# Run the consolidated migrations
php artisan migrate --path=database/migrations/consolidated

# Compare the schemas
php artisan schema:compare

# If everything looks good, prepare for production
# (This part should be done manually with extreme caution)
``` 
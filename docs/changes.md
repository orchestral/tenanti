---
title: Tenanti Change Log

---

## Version 3.2 {#v3-2}

### v3.2.1 {#v3-2-1}

* Rework on how configuration are being pull for multi database connection.
* Add third parameters for `Orchestra\Tenanti\TenantiManager::connection()` to allow filtering by `except` or `only`. This allows only selection of driver to utilize multi database connection.

### v3.2.0 {#v3-2-0}

* Update support to Laravel Framework v5.2.
* Improved performances by reducing call within `Illuminate\Container\Container`.
* Add `Orchestra\Tenanti\Migrator\OperationTrait::resolveDatabaseConnection()` and `Orchestra\Tenanti\Migrator\OperationTrait::asDefaultConnection()` to allow resolving dynamic database connection for multiple database connection setup.
* Add `Orchestra\Tenanti\TenantiManager::connection()`.

## Version 3.1 {#3-1}

### v3.1.6 {#v3-1-6}

* Rework on how configuration are being pull for multi database connection.
* Add third parameters for `Orchestra\Tenanti\TenantiManager::connection()` to allow filtering by `except` or `only`. This allows only selection of driver to utilize multi database connection.

### v3.1.5 {#v3-1-5}

* Allow Tenant job to be configurable.
* Add `Orchestra\Tenanti\TenantiManager::connection()` and deprecate `Orchestra\Tenanti\TenantiManager::setupMultipleDatabase()`.
* Add `Orchestra\Tenanti\Migrator\OperationTrait::asDefaultConnection()` and deprecate `Orchestra\Tenanti\Migrator\OperationTrait::asDefaultDatabase()`.

### v3.1.4 {#v3-1-4}

* Add `Orchestra\Tenanti\Migrator\OperationTrait::resolveDatabaseConnection()` and `Orchestra\Tenanti\Migrator\OperationTrait::asDefaultDatabase()` to allow resolving dynamic database connection for multiple database connection setup.
* Add `Orchestra\Tenanti\TenantiManager::setupMultipleDatabase()`.

### v3.1.3 {#v3-1-3}

* Fixes generated migration file schema.

### v3.1.2 {#v3-1-2}

* Fixes migration is failing on some scenarios when using read/write connection setup.

### v3.1.1 {#v3-1-1}

* Improved performances by reducing call within `Illuminate\Container\Container`.

### v3.1.0 {#v3-1-0}

* Update support to Laravel Framework v5.1.
* Allow different connection name to be used when resolving migration.
* Allow multiple command to allow `--id` option on action command ("install", "migrate", "rollback", "reset" or "refresh").
* Add `php artisan tenanti:queue` to run action command ("install", "migrate", "rollback", "reset" or "refresh") via background queue workers.
* Pass `--force` to all child command.

## Version 3.0 {#3-0}

### v3.0.4 {#v3-0-4}

* Allow multiple command to allow `--id` option on action command ("install", "migrate", "rollback", "reset" or "refresh").
* Add `php artisan tenanti:queue` to run action command ("install", "migrate", "rollback", "reset" or "refresh") via background queue workers.
* Pass `--force` to all child command.

### v3.0.3 {#v3-0-3}

* Use `Illuminate\Foundation\Composer::dumpAutoloads()` directly and remove deprecated call to `$this->call('dump-autoload');`.
* Use available `database_path()` helper.

### v3.0.2 {#v3-0-2}

* Add fallback support to Laravel 5 configuration.

### v3.0.1 {#v3-0-1}

* Allow different connection name to be used when resolving migration.

### v3.0.0 {#v3-0-0}

* Update support to Laravel Framework v5.0.
* Simplify PSR-4 path.

## Version 2.2 {#v2-2}

### v2.2.4 {#v2-2-4}

* Allow different connection name to be used when resolving migration.

### v2.2.3 {#v2-2-3}

* Allow migration note to be available when running command.
* Fixes `--pretend` command not passing entity key and entity instance.
* Utilize `Illuminate\Support\Arr`.
* Add `chunk` option to configuration file.

### v2.2.2 {#v2-2-2}

* Fixes `Orchestra\Tenanti\Migrator\OperationTrait::bindWithKey()` to not convert the `$name` to empty string when given `NULL`.

### v2.2.1 {#v2-2-1}

* Allow to specifically declare database connection name when running `Orchestra\Tenanti\Observer`. This allow user to setup multi-tenancy on a different database.
* Allow to configuration custom multi-tenant migration table name.
* Bind tenant key to database name, which allow using multi-tenancy database using multiple database connection.

### v2.2.0 {#v2-2-0}

* Initial release.

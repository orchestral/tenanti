# Release Note for 5.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/tenanti`.

## 5.0.1

Released: 2020-04-03

### Changes

* Throw exception when trying to make migration file without `--table` option on shared database configuration.

### Fixes

* Fixes migration stub files.

## 5.0.0

Released: 2020-04-03

### Added

* Added `Orchestra\Tenanti\Migrator\MigrationWriter`.
* Added `usingConnection()` and `outputUsing()` helper method to `Orchestra\Tenanti\Migrator\Migrator`.

### Changes

* Update support for Laravel Framework v5.
* Replace the following on `Orchestra\Tenanti\Migrator\Operation`:
    - `executeFor()` with `find()`.
    - `executeForEach()` with `each()`.
    - `getModel()` with `model()`.
    - `getModelName()` with `modelName()`.
    - `resolveMigrator()` with `migrator()`.
    - `asConnection` with `connectionName()`
    - `bindWithKey()` with `nomalize()`.
    - `resolveConnection()` with `connection()`.
    - `resolveMigrationTableName()` with `migrationTableName()`.
    - `getTablePrefix()` with `tablePrefix()`.

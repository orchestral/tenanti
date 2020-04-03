# Release Note for 4.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/tenanti`.

## 4.1.0

Released: 2020-01-04

### Added

* Set command exit code.
* Added `tenant()`, `tenantDriver()` and `tenantDriverName()` to `Orchestra\Tenanti\Console\BaseCommand`.

## 4.0.0

Released: 2019-09-03

### Changes

* Update support for Laravel Framework v6.
* Improves support for Lumen Framework.

### Breaking Changes

* Configuration file options for `path` need to be updated to `paths` which only access an array or migration paths.
* Rename `Orchestra\Tenanti\TenantiManager::getConfig()` to `Orchestra\Tenanti\TenantiManager::config()`.

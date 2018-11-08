# Changelog for 3.5

This changelog references the relevant changes (bug and security fixes) done to `orchestra/tenanti`.

## 3.5.2

Released: 2018-11-07

### Changes

* Disconnect database after preparing migrations table for each tenant.

## 3.5.1

Released: 2018-01-08

### Added

* Added `Orchestra\Tenanti\Tenantor`.
* Added `Orchestra\Tenanti\Eloquent\Tenantee` trait.

### Fixes

* Fixes `Orchestra\Tenanti\Contracts\Factory` contracts.

## 3.5.0

Released: 2017-11-13

### Changes

* Update support for Laravel Framework v5.5.
* Utilize fetching instance of model collection via `cursor()`.

### Fixes

* Tenant creation and deletion job should attempt to use given model instead of querying the database again.
* Reset database connection after migration if possible.

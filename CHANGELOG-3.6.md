# Changelog for 3.6

This changelog references the relevant changes (bug and security fixes) done to `orchestra/tenanti`.

## 3.6.5

Released: 2018-08-11

### Changes

* Force to use write PDO connection when migrating tenants.

## 3.6.4

Released: 2018-08-07

### Changes

* Import `Illuminate\Database\Schema\Blueprint` and `Illuminate\Support\Facades\Schema` to `blank` stub by default.

## 3.6.3

Released: 2018-08-01

### Changes

* Add `--delay` options to `tenanti:queue` command.

### Fixes

* Readd missing `--force` options to `tenanti:queue` command due to `Illuminate\Console\ConfirmableTrait` usage.

## 3.6.2

Released: 2018-07-30

### Added

* Added `Orchestra\Tenanti\Contracts\TenantProvider`.

### Changes

* Allow `Orchestra\Tenanti\Model::tenant` to accept `Orchestra\Tenanti\Contracts\TenantProvider`.

## 3.6.1

Released: 2018-07-28

### Added

* Added `Orchestra\Tenanti\Eloquent\Model`.
* Added `Orchestra\Tenanti\Eloquent\Builder`.
* Added `Orchestra\Tenanti\Tenantor::fromEloquent`.

## 3.6.0

Released: 2018-05-02

### Changes

* Update support for Laravel Framework v5.6.

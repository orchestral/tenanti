# Changelog for 3.6

This changelog references the relevant changes (bug and security fixes) done to `orchestra/tenanti`.

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

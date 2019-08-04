# Release Note for v3.8

This changelog references the relevant changes (bug and security fixes) done to `orchestra/tenanti`.

## 3.8.3

Released: 2019-08-04

### Changes

* Use `static function` rather than `function` whenever possible, the PHP engine does not need to instantiate and later GC a `$this` variable for said closure.

## 3.8.2

Released: 2019-07-05

### Fixes

* Fixed array to string conversion for migration `paths` when running `php artisan tenanti:make` command.

## 3.8.1

Released: 2019-04-28

### Changes

* Allow multiple migration `paths`.

## 3.8.0

Released: 2019-03-31

### Changes

* Update support for Laravel Framework v5.8.

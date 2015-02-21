---
title: Tenanti Change Log

---

## Version 3.0 {#3-0}

### v3.0.0 {#v3-0-0}

* Update support to Laravel Framework v5.0.
* Simplify PSR-4 path.

## Version 2.2 {#v2-2}

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

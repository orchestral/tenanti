# Release Note for v4.0

This changelog references the relevant changes (bug and security fixes) done to `orchestra/tenanti`.


## 4.0.0

### Changes

* Update support for Laravel Framework 6.0+.
* Improves support for Lumen Framework.

### Breaking Changes

* Configuration file options for `path` need to be updated to `paths` which only access an array or migration paths.
* Rename `Orchestra\Tenanti\TenantiManager::getConfig()` to `Orchestra\Tenanti\TenantiManager::config()`.

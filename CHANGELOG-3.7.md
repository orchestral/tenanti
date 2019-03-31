# Changelog for 3.7

This changelog references the relevant changes (bug and security fixes) done to `orchestra/tenanti`.

## 3.7.1

Released: 2019-03-13

### Changes

* Improve performance by prefixing all global functions calls with `\` to skip the look up and resolve process and go straight to the global function.

## 3.7.0

Released: 2018-11-19

### Changes

* Update support for Laravel Framework v5.7.
* Disconnect database after preparing migrations table for each tenant.
* Allow tenanti queue connection to be configurable via config and environment file.

Multi-tenant Database Schema Manager for Laravel
==============

Tenanti allow you to manage multi-tenant data schema and migration manager for your Laravel application.

[![Build Status](https://travis-ci.org/orchestral/tenanti.svg?branch=3.8)](https://travis-ci.org/orchestral/tenanti)
[![Latest Stable Version](https://poser.pugx.org/orchestra/tenanti/v/stable)](https://packagist.org/packages/orchestra/tenanti)
[![Total Downloads](https://poser.pugx.org/orchestra/tenanti/downloads)](https://packagist.org/packages/orchestra/tenanti)
[![Latest Unstable Version](https://poser.pugx.org/orchestra/tenanti/v/unstable)](https://packagist.org/packages/orchestra/tenanti)
[![License](https://poser.pugx.org/orchestra/tenanti/license)](https://packagist.org/packages/orchestra/tenanti)
[![Coverage Status](https://coveralls.io/repos/github/orchestral/tenanti/badge.svg?branch=3.8)](https://coveralls.io/github/orchestral/tenanti?branch=3.8)

## Table of Content

* [Version Compatibility](#version-compatibility)
* [Installation](#installation)
* [Configuration](#configuration)
* [Usage](#usage)
* [Changelog](https://github.com/orchestral/tenanti/releases)

## Version Compatibility

Laravel  | Tenanti
:--------|:---------
 5.5.x   | 3.5.x
 5.6.x   | 3.6.x
 5.7.x   | 3.7.x
 5.8.x   | 3.8.x

## Installation

To install through composer, simply put the following in your `composer.json` file:

```json
{
    "require": {
        "orchestra/tenanti": "^3.5"
    }
}
```

And then run `composer install` to fetch the package.

### Quick Installation

You could also simplify the above code by using the following command:

    composer require "orchestra/tenanti=^3.5"

## Configuration

Next add the following service provider in `config/app.php`.

```php
'providers' => [

    // ...
    Orchestra\Tenanti\TenantiServiceProvider::class,
    Orchestra\Tenanti\CommandServiceProvider::class,
],
```

> The command utility is enabled via `Orchestra\Tenanti\CommandServiceProvider`.

### Aliases

To make development easier, you could add `Orchestra\Support\Facades\Tenanti` alias for easier reference:

```php
'aliases' => [

    'Tenanti' => Orchestra\Support\Facades\Tenanti::class,

],
```

### Publish Configuration

To make it easier to configuration your tenant setup, publish the configuration:

    php artisan vendor:publish

## Usage

### Configuration Tenant Driver for Single Database

Open `config/orchestra/tenanti.php` and customize the drivers.

```php
<?php

return [
    'drivers' => [
        'user' => [
            'model'  => App\User::class,
            'path'   => database_path('tenanti/user'),
            'shared' => true,
        ],
    ],
];
```

You can customize, or add new driver in the configuration. It is important to note that `model` configuration only work with `Eloquent` instance.

#### Setup migration autoload

For each driver, you should also consider adding the migration path into autoload (if it not already defined). To do this you can edit your `composer.json`.

##### composer.json

```json
{
    "autoload": {
        "classmap": [
            "database/tenant/users"
        ]
    }
}
```

### Setup Tenantor Model

Now that we have setup the configuration, let add an observer to our `User` class:

```php
<?php 

namespace App;

use App\Observers\UserObserver;
use Orchestra\Tenanti\Tenantor;
use Illuminate\Notifications\Notifiable;
use Orchestra\Tenanti\Contracts\TenantProvider;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements TenantProvider
{
    use Notifiable;

    /**
     * Convert to tenantor.
     * 
     * @return \Orchestra\Tenanti\Tenantor
     */
    public function asTenantor(): Tenantor
    {
        return Tenantor::fromEloquent('user', $this);
    }

    /**
     * Make a tenantor.
     *
     * @return \Orchestra\Tenanti\Tenantor
     */
    public static function makeTenantor($key, $connection = null): Tenantor
    {
        return Tenantor::make(
            'user', $key, $connection ?: (new static())->getConnectionName()
        );
    }

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::observe(new UserObserver);
    }
}
```

and your `App\Observers\UserObserver` class should consist of the following:

```php
<?php 

namespace App\Observers;

use Orchestra\Tenanti\Observer;

class UserObserver extends Observer
{
    public function getDriverName()
    {
        return 'user';
    }
}
```

## Console Support

Tenanti include additional command to help you run bulk migration when a new schema is created, the available command resemble the usage available from `php artisan migrate` namespace.

Command                                      | Description
:--------------------------------------------|:--------------------------------------------
 php artisan tenanti:install {driver}        | Setup migration table on each entry for a given driver.
 php artisan tenanti:make {driver} {name}    | Make a new Schema generator for a given driver.
 php artisan tenanti:migrate {driver}        | Run migration on each entry for a given driver.
 php artisan tenanti:rollback {driver}       | Rollback migration on each entry for a given driver.
 php artisan tenanti:reset {driver}          | Reset migration on each entry for a given driver.
 php artisan tenanti:refresh {driver}        | Refresh migration (reset and migrate) on each entry for a given driver.
 php artisan tenanti:queue {driver} {action} | Execute any of above action using separate queue to minimize impact on current process.
 php artisan tenanti:tinker {driver} {id}    | Run tinker using a given driver and ID.

## Multi Database Connection Setup

Instead of using Tenanti with a single database connection, you could also setup a database connection for each tenant.

### Configuration Tenant Driver for Multiple Database

Open `config/orchestra/tenanti.php` and customize the drivers.

```php
<?php

return [
    'drivers' => [
        'user' => [
            'model'  => App\User::class,
            'path'   => database_path('tenanti/user'),
            'shared' => false,
        ],
    ],
];
```

By introducing a `migration` config, you can now setup the migration table name to be `tenant_migrations` instead of `user_{id}_migrations`.

### Database Connection Resolver

For tenanti to automatically resolve your multiple database connection, we need to setup the resolver. You can do this via:

```php
<?php namespace App\Providers;

use Orchestra\Support\Facades\Tenanti;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Tenanti::connection('tenants', function (User $entity, array $config) {
            $config['database'] = "acme_{$entity->getKey()}"; 
            // refer to config under `database.connections.tenants.*`.

            return $config;
        });
    }
}
```

Behind the scene, `$config` will contain the template database configuration fetch from `"database.connections.tenants"` (based on the first parameter `tenants`). We can dynamically modify the connection configuration and return the updated configuration for the tenant.

### Setting Default Database Connection

Alternatively you can also use Tenanti to set the default database connection for your application:

```php

use App\User;
use Orchestra\Support\Facades\Tenanti;

// ...

$user = User::find(5);

Tenanti::driver('user')->asDefaultConnection($user, 'tenants_{id}');
```

> Most of the time, this would be use in a Middleware Class when you resolve the tenant ID based on `Illuminate\Http\Request` object.

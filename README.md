Multi-tenant Database Schema Manager for Laravel
==============

[![Join the chat at https://gitter.im/orchestral/platform/components](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/orchestral/platform/components?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Tenanti allow you to manage multi-tenant data schema and migration manager for your Laravel application.

[![Latest Stable Version](https://img.shields.io/github/release/orchestral/tenanti.svg?style=flat-square)](https://packagist.org/packages/orchestra/tenanti)
[![Total Downloads](https://img.shields.io/packagist/dt/orchestra/tenanti.svg?style=flat-square)](https://packagist.org/packages/orchestra/tenanti)
[![MIT License](https://img.shields.io/packagist/l/orchestra/tenanti.svg?style=flat-square)](https://packagist.org/packages/orchestra/tenanti)
[![Build Status](https://img.shields.io/travis/orchestral/tenanti/master.svg?style=flat-square)](https://travis-ci.org/orchestral/tenanti)
[![Coverage Status](https://img.shields.io/coveralls/orchestral/tenanti/master.svg?style=flat-square)](https://coveralls.io/r/orchestral/tenanti?branch=master)
[![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/orchestral/tenanti/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/orchestral/tenanti/)

## Table of Content

* [Version Compatibility](#version-compatibility)
* [Installation](#installation)
* [Configuration](#configuration)
* [Usage](#usage)
* [Change Log](http://orchestraplatform.com/docs/latest/components/tenanti/changes#v3-2)

## Version Compatibility

Laravel  | Tenanti
:--------|:---------
 4.2.x   | 2.2.x
 5.0.x   | 3.0.x
 5.1.x   | 3.1.x
 5.2.x   | 3.2.x@dev
 5.3.x   | 3.3.x@dev

## Installation

To install through composer, simply put the following in your `composer.json` file:

```json
{
	"require": {
		"orchestra/tenanti": "~3.0"
	}
}
```

And then run `composer install` to fetch the package.

### Quick Installation

You could also simplify the above code by using the following command:

    composer require "orchestra/tenanti=~3.0"

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
            'model' => App\User::class,
            'path'  => database_path('tenanti/user'),
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

### Setup Model Observer

Now that we have setup the configuration, let add an observer to our `User` class (preferly in `App\Providers\AppServiceProvider`):

```php
<?php namespace App\Providers;

use App\User;
use App\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
{
	public function register()
	{
		User::observe(new UserObserver);
	}
}
```

and your `App\Observers\UserObserver` class should consist of the following:

```php
<?php namespace App\Observers;

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

## Multi Database Connection Setup

Instead of using Tenanti with a single database connection, you could also setup a database connection for each tenant.

### Configuration Tenant Driver for Multiple Database

Open `config/orchestra/tenanti.php` and customize the drivers.

```php
<?php

return [
    'drivers' => [
        'user' => [
            'model'     => App\User::class,
            'migration' => 'tenant_migrations',
            'path'      => database_path('tenanti/user'),
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
        Tenanti::setupMultiDatabase('tenants', function (User $entity, array $template) {
            $template['database'] = "acme_{$entity->getKey()}";

            return $template;
        });
    }
}
```

Behind the scene, `$template` will contain the template database configuration fetch from `"database.connections.tenants"` (based on the first parameter `tenants`). We can dynamically modify the connection configuration and return the updated configuration for the tenant.

### Setting Default Database Connection

Alternatively you can also use Tenanti to set the default database connection for your application:

```php

use App\User;
use Orchestra\Support\Facades\Tenanti;

// ...

$user = User::find(5);

Tenanti::driver('user')->asDefaultDatabase($user, 'tenants_{id}');
```

> Most of the time, this would be use in a Middleware Class when you resolve the tenant ID based on `Illuminate\Http\Request` object.

### Observer

Adding an override method for `getConnectionName()` would allow you to force the migration to be executed on the desire connection.

```php
<?php namespace App\Observers;

use Orchestra\Tenanti\Observer;

class UserObserver extends Observer
{
	public function getDriverName()
	{
		return 'user';
	}

	public function getConnectionName()
	{
		return 'tenants_{id}';
	}
}
```

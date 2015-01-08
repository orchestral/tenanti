Multi-tenant Database Schema Manager for Laravel
==============

Tenanti allow you to manage multi-tenant data schema and migration manager for your Laravel application.

[![Latest Stable Version](https://img.shields.io/github/release/orchestral/tenanti.svg?style=flat)](https://packagist.org/packages/orchestra/tenanti)
[![Total Downloads](https://img.shields.io/packagist/dt/orchestra/tenanti.svg?style=flat)](https://packagist.org/packages/orchestra/tenanti)
[![MIT License](https://img.shields.io/packagist/l/orchestra/tenanti.svg?style=flat)](https://packagist.org/packages/orchestra/tenanti)
[![Build Status](https://img.shields.io/travis/orchestral/tenanti/master.svg?style=flat)](https://travis-ci.org/orchestral/tenanti)
[![Coverage Status](https://img.shields.io/coveralls/orchestral/tenanti/master.svg?style=flat)](https://coveralls.io/r/orchestral/tenanti?branch=master)
[![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/orchestral/tenanti/master.svg?style=flat)](https://scrutinizer-ci.com/g/orchestral/tenanti/)

## Version Compatibility

Laravel  | Tenanti
:--------|:---------
 4.2.x   | 2.2.x
 5.0.x   | 3.0.x@dev

## Installation

To install through composer, simply put the following in your `composer.json` file:

```json
{
	"require": {
		"orchestra/tenanti": "3.0.*"
	}
}
```

And then run `composer install` to fetch the package.

### Quick Installation

You could also simplify the above code by using the following command:

```
composer require "orchestra/tenanti=3.0.*"
```

### Setup

Next add the following service provider in `app/config/app.php`.

```php
'providers' => [

	// ...
	'Orchestra\Tenanti\TenantiServiceProvider',
	'Orchestra\Tenanti\CommandServiceProvider',
],
```

> The command utility is enabled via Orchestra\Tenanti\CommandServiceProvider.

## Usage

### Configuration

Update your `App\Providers\ConfigServiceProvider` to include following options:

```php
<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{
	public function register()
	{
		config([
			'orchestra/tenanti::drivers.user' => [
				'model' => 'App\User',
				'path'  => base_path('database/tenanti/user'),
			],
		]);
	}
}
```

You can customize, or add new driver in the configuration. It is important to note that `model` configuration only work with `Eloquent` instance.

#### Setup migration autoload

For each driver, you should also consider adding the migration path into autoload. To do this you can edit your `composer.json`.

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

Command                                    | Description
:------------------------------------------|:---------------------
 php artisan tenanti:install {driver}      | Setup migration table on each entry for a given driver.
 php artisan tenanti:make {driver} {name}  | Make a new Schema generator for a given driver.
 php artisan tenanti:migrate {driver}      | Run migration on each entry for a given driver.
 php artisan tenanti:rollback {driver}     | Rollback migration on each entry for a given driver.
 php artisan tenanti:reset {driver}        | Reset migration on each entry for a given driver.
 php artisan tenanti:refresh {driver}      | Refresh migration (reset and migrate) on each entry for a given driver.

## Multi Database Connection Setup

Instead of using Tenanti with a single database connection, you could also setup a database connection for each tenant.

### Configuration

By introducing a `migration` config, you can now setup the migration table name to be `tenant_migrations` instead of `user_{id}_migrations`.

```php
<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{
    public function register()
    {
        config([
            'orchestra/tenanti::drivers.user' => [
                'model'     => 'App\User',
                'migration' => 'tenant_migrations',
                'path'      => base_path('database/tenanti/user'),
            ],
        ]);
    }
}
```

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
		return 'tenant_{id}';
	}
}
```

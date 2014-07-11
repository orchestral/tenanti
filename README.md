Tenant DB Schema Manager for Laravel
==============

[![Latest Stable Version](https://poser.pugx.org/orchestra/tenanti/v/stable.png)](https://packagist.org/packages/orchestra/tenanti) 
[![Total Downloads](https://poser.pugx.org/orchestra/tenanti/downloads.png)](https://packagist.org/packages/orchestra/tenanti) 
[![Build Status](https://travis-ci.org/orchestral/tenanti.svg?branch=master)](https://travis-ci.org/orchestral/tenanti) 
[![Coverage Status](https://coveralls.io/repos/orchestral/tenanti/badge.png?branch=master)](https://coveralls.io/r/orchestral/tenanti?branch=master) 
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/orchestral/tenanti/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/orchestral/tenanti/) 

## Version Compatibility

Laravel  | Tenanti
:--------|:---------
 4.2.x   | 2.2.x

## Installation

To install through composer, simply put the following in your `composer.json` file:
 
```json
{
	"require": {
		"orchestra/tenanti": "2.2.*"
	}	
}
```

And then run `composer install` to fetch the package.

### Quick Installation

You could also simplify the above code by using the following command:

```
composer require "orchestra/tenanti=2.2.*"
```

## Usage

### Configuration

First, let's export the configuration to your application configuration folder to customize the option:

```
php artisan config:publish orchestra/tenanti
```

### Setup Model Observer

Now that we have setup the configuration, let add an observer to our `User` class (preferly in `app/start/global.php`):

```php
<?php

use Orchestra\Tenanti\Observer;

User::observe(new Observer('user'));
```
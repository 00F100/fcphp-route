# FcPHP Route

Package do manage routes into FcPhp

[![Build Status](https://travis-ci.org/00F100/fcphp-route.svg?branch=master)](https://travis-ci.org/00F100/fcphp-route) [![codecov](https://codecov.io/gh/00F100/fcphp-route/branch/master/graph/badge.svg)](https://codecov.io/gh/00F100/fcphp-route) [![Total Downloads](https://poser.pugx.org/00F100/fcphp-route/downloads)](https://packagist.org/packages/00F100/fcphp-route)

## How to install

Composer:
```sh
$ composer require 00f100/fcphp-route
```

or composer.json
```json
{
    "require": {
        "00f100/fcphp-route": "*"
    }
}
```

## How to use

```php
<?php

use FcPhp\Di\Facades\DiFacade;
use FcPhp\Cache\Facades\CacheFacade;
use FcPhp\SHttp\SEntity;
use FcPhp\Autoload\Autoload;
use FcPhp\Route\RouteFactory;
use FcPhp\Route\Route;


###########
# PREPARE
###########

    // Dependency injection to manipulate new instance of Entity
    $di = DiFacade::getInstance();
    $factory = new RouteFactory($di);

    // Security Entity (see: https://github.com/00f100/fcphp-shttp)
    $entity = new SEntity();

    // Autoload array files (see: https://github.com/00f100/fcphp-autoload)
    $autoload = new Autoload();
    // Path to Autoload use
    $vendorPath = 'vendor/*/*/config';

    // Cache information (see: https://github.com/00f100/fcphp-cache)
    $cache = CacheFacade::getInstance('tests/var/cache');



###########
# EXECUTE
###########

// New instance of route
$instance = new Route($entity, $autoload, $cache, $vendorPath, $factory);

// Match route into routes list
$match = $instance->match('GET', 'v1/users/10');

// Print: 200
echo $match->getStatusCode();
```
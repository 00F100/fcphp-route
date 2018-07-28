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

    // Dependency injection  (see: https://github.com/00f100/fcphp-di)
    $di = DiFacade::getInstance();

    // Factory to create new instance of FcPhp\Route\Entity
    $factory = new RouteFactory($di);

    // Security Entity (see: https://github.com/00f100/fcphp-shttp)
    $entity = new SEntity();

    // Autoload array files (see: https://github.com/00f100/fcphp-autoload)
    $autoload = new Autoload();

    // Path to Autoload use
    $vendorPath = 'vendor/*/*/config';

    // Cache information (see: https://github.com/00f100/fcphp-cache)
    $cache = CacheFacade::getInstance('path/to/cache');


###########
# EXECUTE
###########

// New instance of route
$instance = new Route($entity, $autoload, $cache, $vendorPath, $factory);


###########
# CALLBACK
###########

// Init match route process
$this->instance->callback('initCallback', function(array $routes) {

    // Your code here ...

});

// Match route
$this->instance->callback('matchCallback', function(array $routes, string $method, string $route, array $entity, IEntity $routeEntity) {

    // Your code here ...

});

// Route not found
$this->instance->callback('notFoundCallback', function(array $routes, string $method, string $route, array $entity = [], IEntity $routeEntity = null) {

    // Your code here ...

});


###########
# MATCH ROUTE
###########

// Match route into routes list
$match = $instance->match('GET', 'v1/users/10');

// Print: FcPhp\Route\Entity
echo get_class($match);

// Print: 200
echo $match->getStatusCode();
```

##### [FcPhp\Route\Entity](https://github.com/00F100/fcphp-route/blob/master/src/Interfaces/IEntity.php)

```php
<?php

namespace FcPhp\Route\Interfaces
{
    interface IEntity
    {
        /**
         * Method to construct instance
         *
         * @param array $params Params to populate Entity
         * @return void
         */
        public function __construct(array $params = []);

        /**
         * Method to return method of request
         *
         * @return string
         */
        public function getMethod() :string;

        /**
         * Method to return route of request
         *
         * @return string|null
         */
        public function getRoute();

        /**
         * Method to return rule to access
         *
         * @return string|null
         */
        public function getRule();

        /**
         * Method to return action to execute
         *
         * @return string|null
         */
        public function getAction();

        /**
         * Method to return filters to apply
         *
         * @return array
         */
        public function getFilter() :array;

        /**
         * Method to return status code
         *
         * @return int
         */
        public function getStatusCode();

        /**
         * Method to return status message
         *
         * @return string|null
         */
        public function getStatusMessage();

        /**
         * Method to configure full route
         *
         * @param string $fullRoute Full route
         * @return void
         */
        public function setFullRoute(string $fullRoute) :void;

        /**
         * Method to return full route
         *
         * @return string|null
         */
        public function getFullRoute();

        /**
         * Method to configure params to controller
         *
         * @param array $params Params to controller
         * @return void
         */
        public function setParams(array $params) :void;

        /**
         * Method to return params to controller
         *
         * @return array
         */
        public function getParams() :array;
    }
}
```
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

After match route (same 404, 403) return instance of [FcPhp\Route\Entity](https://github.com/00F100/fcphp-route/blob/master/src/Interfaces/IEntity.php)

```php
<?php

use FcPhp\SHttp\SEntity;
use FcPhp\Route\Facades\RouteFacade;

// See: https://github.com/00F100/fcphp-shttp
$entity = new SEntity();

// Config directories to autoload and cache
$vendorPath = 'tests/*/*/config';
$cachePath = 'tests/var/cache';

// Init instance of Route
$instance = RouteFacade::getInstance($entity, $vendorPath, $cachePath);

// Match route into routes list
$match = $instance->match('GET', 'v1/users/10');

// Print: FcPhp\Route\Entity
echo get_class($match);

// Print: 200
echo $match->getStatusCode();
```

##### Callback's

```php
<?php

use FcPhp\Route\Interfaces\IEntity;

// Init match route process
$instance->callback('initCallback', function(array $routes) {

    // Your code here ...

});

// Match route
$instance->callback('matchCallback', function(array $routes, string $method, string $route, array $entity, IEntity $routeEntity) {

    // Your code here ...

});

// Route not found
$instance->callback('notFoundCallback', function(array $routes, string $method, string $route, array $entity = [], IEntity $routeEntity = null) {

    // Your code here ...

});
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
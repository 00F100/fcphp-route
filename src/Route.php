<?php

namespace FcPhp\Route
{
    use FcPhp\Cache\Interfaces\ICache;
    use FcPhp\SHttp\Interfaces\ISEntity;
    use FcPhp\Autoload\Interfaces\IAutoload;
    use FcPhp\Route\Interfaces\{IRoute, IRouteFactory, IEntity};

    class Route implements IRoute
    {
        const TTL_ROUTE = 84000;

        /**
         * @var string Key of cache
         */
        private $key;

        /**
         * @var FcPhp\SHttp\Interfaces\ISEntity
         */
        private $entity;

        /**
         * @var FcPhp\Cache\Interfaces\ICache
         */
        private $cache;

        /**
         * @var FcPhp\Autoload\Interfaces\IAutoload
         */
        private $autoload;

        /**
         * @var FcPhp\Route\Interfaces\IRouteFactory
         */
        private $factory;

        /**
         * @var array $routes
         */
        public $routes = [];

        /**
         * @var object Callback init match
         */
        private $initCallback;

        /**
         * @var object Callback match route
         */
        private $matchCallback;

        /**
         * @var object Callback not found route
         */
        private $notFoundCallback;

        /**
         * Method to construct instance of Route
         *
         * @param FcPhp\SHttp\Interfaces\ISEntity $entity Security Entity
         * @param FcPhp\Autoload\Interfaces\IAutoload $autoload Instance of Autoload
         * @param FcPhp\Cache\Interfaces\ICache $cache Instance of Cache
         * @param string $vendorPath Vendor path to Autoload
         * @param FcPhp\Route\Interfaces\IRouteFactory $factory Instance of Route Factory
         * @param bool $noCache No use cache
         * @return void
         */
        public function __construct(ISEntity $entity, IAutoload $autoload, ICache $cache, string $vendorPath, IRouteFactory $factory, bool $noCache = false, array $routes = [])
        {
            $this->key = md5(serialize($vendorPath) . serialize($routes));
            $this->entity = $entity;
            $this->cache = $cache;
            $this->autoload = $autoload;
            $this->factory = $factory;
            $this->routes = $this->cache->get($this->key);
            if(empty($this->routes)) {
                $this->routes = $routes;
                $this->autoload->path($vendorPath, ['routes'], ['php']);
                $this->routes = $this->merge($this->routes, $this->autoload->get('routes'));
                $this->fixRoutes();
                if(!$noCache) {
                    $this->cache->set($this->key, $this->routes, self::TTL_ROUTE);
                }
            }
        }

        /**
         * Method to find match between route and cache
         *
         * @param string $method Method to find
         * @param string $route Route to find
         * @return FcPhp\Route\Interfaces\IEntity
         */
        public function match(string $method, string $route) :IEntity
        {
            $routeEntity = null;
            $route = explode('?', $route);
            $route = current($route);
            $this->initCallback($this->routes, $method, $route);
            if(isset($this->routes[$method])) {
                $itemRoute = explode('/', $route);
                foreach($this->routes[$method] as $possibleRoute => $entity) {
                    $itemPossibleRoute = explode('/', $possibleRoute);
                    if(count($itemRoute) == count($itemPossibleRoute)){
                        foreach($itemRoute as $index => $part) {
                            $possiblePart = $itemPossibleRoute[$index];
                            if(substr($possiblePart, 0, 1) != '{') {
                                if($part == $itemPossibleRoute[$index]) {
                                    $partCurrent[] = $part;
                                }else{
                                    $partCurrent = [];
                                    $params = [];
                                    break;
                                }
                            }else{
                                $partCurrent[] = $part;
                                $params[] = $part;
                            }
                            if(count($itemRoute)-1 == $index) {
                                if(count($partCurrent) == count($itemRoute)) {
                                    $entity['params'] = [];
                                    if(isset($params)) {
                                        $entity['params'] = $params;
                                    }

                                    $routeEntity = $this->factory->getEntity($entity);
                                    if(!is_null($routeEntity->getRule())) {
                                        if(!$this->entity->check($routeEntity->getRule())) {
                                            $routeEntity = $this->factory->getEntity([
                                                'method' => $method,
                                                'route' => $route,
                                                'statusCode' => 403,
                                                'statusMessage' => 'Forbidden',
                                            ]);
                                        }
                                    }
                                    $this->matchCallback($this->routes, $method, $route, $entity, $routeEntity);
                                }
                            }
                        }
                    }
                }
            }
            if(is_null($routeEntity)) {
                $routeEntity = $this->factory->getEntity([
                    'method' => $method,
                    'route' => $route,
                    'statusCode' => 404,
                    'statusMessage' => 'Not Found',
                ]);
                $this->notFoundCallback($this->routes, $method, $route, (isset($entity) ? $entity : []), (isset($routeEntity) ? $routeEntity : null));
            }
            return $routeEntity;
        }

        /**
         * Method to configure callback
         *
         * @param string $name Name of callback
         * @param object $callback Callback to execute
         * @return void
         */
        public function callback(string $name, object $callback)  :void
        {
            if(property_exists($this, $name)) {
                $this->{$name} = $callback;
            }
        }

        /**
         * Method to fix routes
         *
         * @return void
         */
        private function fixRoutes() :void
        {
            $routeMap = [];
            foreach($this->routes as $version => $routes) {
                $routeBase = $version;
                foreach($routes as $index => $route) {
                    $this->defaults($route);
                    $printRoute = $routeBase;
                    if(!empty($route['route'])) {
                        $printRoute .= '/' . $route['route'];
                    }
                    $route['fullRoute'] = $printRoute;
                    $this->addRouteMap($routeMap, $route['method'], $printRoute, $route);
                }
            }
            $this->routes = $routeMap;
        }

        /**
         * Method to add routes into map
         *
         * @param array $routeMap Map of routes
         * @param string $method Method to find
         * @param string $route Route to find
         * @param array $entity Entity of Route
         * @return void
         */
        private function addRouteMap(array &$routeMap, string $method, string $route, array $entity) :void
        {
            if(!isset($routeMap[$method])) {
                $routeMap[$method] = [];
            }
            $routeMap[$method][$route] = $entity;
        }

        /**
         * Method to add routes into map
         *
         * @param array $route Configuration to route
         * @return void
         */
        private function defaults(array &$route) :void
        {
            $defaults = [
                'statusCode' => 200,
                'method' => 'GET',
                'route' => null,
                'rule' => null,
                'action' => null,
                'filter' => [],
            ];
            $route = array_merge($defaults, $route);
        }

        /**
         * Method to merge routes
         *
         * @param array $array1 Array Route A
         * @param array $array2 Array Route B
         * @param array ...
         * @return array
         */
        private function merge() :array
        {
            $routes = [];
            $listRoutes = func_get_args();

            foreach($listRoutes as $item) {
                foreach($item as $route => $params) {
                    if(!isset($routes[$route])) {
                        $routes[$route] = [];
                    }
                    foreach($params as $param) {
                        $routes[$route][] = $param;
                    }
                }
            }
            return $routes;
        }

        /**
         * Method to configure callback
         *
         * @param array $routes List of routes
         * @param string $method Method of request
         * @param string $route Route to match
         * @return void
         */
        private function initCallback(array $routes, string $method, string $route) :void
        {
            if(!is_null($this->initCallback)) {
                $initCallback = $this->initCallback;
                $initCallback($routes, $method, $route);
            }
        }

        /**
         * Method to configure callback
         *
         * @param array $routes List of routes
         * @param string $method Method of request
         * @param string $route Route to match
         * @param array $entity Entity into array
         * @param FcPhp\Route\Interfaces\IEntity $routeEntity Entity into class
         * @return void
         */
        private function matchCallback(array $routes, string $method, string $route, array $entity, IEntity $routeEntity) :void
        {
            if(!is_null($this->matchCallback)) {
                $matchCallback = $this->matchCallback;
                $matchCallback($routes, $method, $route, $entity, $routeEntity);
            }
        }

        /**
         * Method to configure callback
         *
         * @param array $routes List of routes
         * @param string $method Method of request
         * @param string $route Route to match
         * @param array $entity Entity into array
         * @param FcPhp\Route\Interfaces\IEntity $routeEntity Entity into class
         * @return void
         */
        private function notFoundCallback(array $routes, string $method, string $route, array $entity = [], IEntity $routeEntity = null) :void
        {
            if(!is_null($this->notFoundCallback)) {
                $notFoundCallback = $this->notFoundCallback;
                $notFoundCallback($routes, $method, $route, $entity, $routeEntity);
            }
        }
    }
}

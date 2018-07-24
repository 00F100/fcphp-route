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
        public function __construct(ISEntity $entity, IAutoload $autoload, ICache $cache, string $vendorPath, IRouteFactory $factory, bool $noCache = false)
        {
            $this->key = md5('routes');
            $this->entity = $entity;
            $this->cache = $cache;
            $this->autoload = $autoload;
            $this->factory = $factory;
            if(empty($this->cache->get($this->key))) {
                $this->autoload->path($vendorPath, ['routes'], ['php']);
                $this->routes = array_merge($this->routes, $this->autoload->get('routes'));
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
                                    $entity->setParams($params);
                                    $routeEntity = $entity;
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
            }
            return $routeEntity;
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
                    if(!empty($route->getRoute())) {
                        $printRoute .= '/' . $route->getRoute();
                    }
                    $route->setFullRoute($printRoute);
                    $this->addRouteMap($routeMap, $route->getMethod(), $printRoute, $route);
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
         * @param FcPhp\Route\Interfaces\IEntity $entity Entity of Route
         * @return void
         */
        private function addRouteMap(array &$routeMap, string $method, string $route, IEntity $entity)
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
        private function defaults(array &$route)
        {
            $defaults = [
                'statusCode' => 200,
                'method' => 'GET',
                'route' => null,
                'rule' => null,
                'action' => null,
                'filter' => [],
            ];
            $route = $this->factory->getEntity(array_merge($defaults, $route));
        }
    }
}
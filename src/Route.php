<?php

namespace FcPhp\Route
{
    use FcPhp\Cache\Interfaces\ICache;
    use FcPhp\Route\Interfaces\IRoute;
    use FcPhp\SHttp\Interfaces\ISEntity;
    use FcPhp\Route\Interfaces\IEntity;
    use FcPhp\Autoload\Interfaces\IAutoload;
    use FcPhp\Route\Interfaces\IRouteFactory;

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

        public function match(string $method, string $route)
        {
            if(isset($this->routes[$method])) {
                $itemRoute = explode('/', $route);
                $routeEntity = null;
                foreach($this->routes[$method] as $possibleRoute => $entity) {
                    $itemPossibleRoute = explode('/', $possibleRoute);
                    foreach($itemRoute as $index => $part) {
                        if(count($itemRoute) < count($itemPossibleRoute)){
                            $partCurrent = [];
                            break;
                        }
                        if(!isset($itemPossibleRoute[$index])) {
                            $partCurrent = [];
                            break;
                        }
                        $possiblePart = $itemPossibleRoute[$index];
                        if(substr($possiblePart, 0, 1) != '{') {
                            if($part == $itemPossibleRoute[$index]) {
                                $partCurrent[] = $part;
                            }else{
                                $partCurrent = [];
                                break;
                            }
                        }else{
                            $partCurrent[] = $part;
                        }
                        if(count($itemRoute)-1 == $index) {
                            if(count($partCurrent) > 0) {
                                $routeEntity = $entity;
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

        private function fixRoutes()
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

        private function addRouteMap(array &$routeMap, string $method, string $route, IEntity $entity)
        {
            if(!isset($routeMap[$method])) {
                $routeMap[$method] = [];
            }
            $routeMap[$method][$route] = $entity;
        }

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
<?php

namespace FcPhp\Route\Facades
{
    use FcPhp\Di\Facades\DiFacade;
    use FcPhp\Cache\Facades\CacheFacade;
    use FcPhp\Route\Factories\RouteFactory;
    use FcPhp\SHttp\Interfaces\ISEntity;
    use FcPhp\Route\Interfaces\IRoute;
    use FcPhp\Autoload\Autoload;
    use FcPhp\Route\Route;

    class RouteFacade
    {
        private static $instance = [];

        public static function getInstance(ISEntity $entity, string $vendorPath, string $cachePath, array $routes = [])
        {
            $key = md5(serialize($vendorPath) . serialize($routes));
            if(!isset(self::$instance[$key]) || self::$instance[$key] instanceof IRoute) {
                $di = DiFacade::getInstance();
                $factory = new RouteFactory($di);
                $autoload = new Autoload();
                $cache = CacheFacade::getInstance($cachePath);
                self::$instance[$key] = new Route($entity, $autoload, $cache, $vendorPath, $factory, false, $routes);
            }
            return self::$instance[$key];
        }
    }
}
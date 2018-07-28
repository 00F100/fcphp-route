<?php

namespace FcPhp\Route\Interfaces
{
    use FcPhp\SHttp\Interfaces\ISEntity;
    use FcPhp\Autoload\Interfaces\IAutoload;
    use FcPhp\Cache\Interfaces\ICache;
    use FcPhp\Route\Interfaces\{IEntity, IRouteFactory};

    interface IRoute
    {
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
        public function __construct(ISEntity $entity, IAutoload $autoload, ICache $cache, string $vendorPath, IRouteFactory $factory, bool $noCache = false);

        /**
         * Method to find match between route and cache
         *
         * @param string $method Method to find
         * @param string $route Route to find
         * @return FcPhp\Route\Interfaces\IEntity
         */
        public function match(string $method, string $route) :IEntity;

        /**
         * Method to configure callback
         *
         * @param string $name Name of callback
         * @param object $callback Callback to execute
         * @return void
         */
        public function callback(string $name, object $callback)  :void;
    }
}
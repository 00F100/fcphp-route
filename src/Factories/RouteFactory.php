<?php

namespace FcPhp\Route\Factories
{
    use FcPhp\Route\Entity;
    use FcPhp\Route\Interfaces\IRouteFactory;
    use FcPhp\Di\Interfaces\IDi;
    use FcPhp\Route\Interfaces\IEntity;

    class RouteFactory implements IRouteFactory
    {
        public function __construct(IDi $di = null)
        {
            $this->di = $di;
        }

        public function getEntity(array $params = []) :IEntity
        {
            if($this->di instanceof IDi) {
                return $this->di->make('FcPhp/Route/Entity', [$params]);
            }
            return new Entity($params);
        }
    }
}
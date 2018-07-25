<?php

namespace FcPhp\Route\Factories
{
    use FcPhp\Route\Entity;
    use FcPhp\Route\Interfaces\IRouteFactory;
    use FcPhp\Di\Interfaces\IDi;
    use FcPhp\Route\Interfaces\IEntity;

    class RouteFactory implements IRouteFactory
    {
        /**
         * Method to construct instance
         *
         * @param FcPhp\Di\Interfaces\IDi $di Instance of Di 
         * @return void
         */
        public function __construct(IDi $di = null)
        {
            $this->di = $di;
        }
        /**
         * Method to construct instance of Entity
         *
         * @param array $params Params to Entity
         * @return FcPhp\Route\Interfaces\IEntity
         */
        public function getEntity(array $params = []) :IEntity
        {
            if($this->di instanceof IDi) {
                if(!$this->di->has('FcPhp/Route/Entity')) {
                    $this->di->setNonSingleton('FcPhp/Route/Entity', 'FcPhp\Route\Entity');
                }
                return $this->di->make('FcPhp/Route/Entity', ['params' => $params]);
            }
            return new Entity($params);
        }
    }
}
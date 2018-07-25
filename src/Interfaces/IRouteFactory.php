<?php

namespace FcPhp\Route\Interfaces
{
    use FcPhp\Di\Interfaces\IDi;
    
    interface IRouteFactory
    {
        /**
         * Method to construct instance
         *
         * @param FcPhp\Di\Interfaces\IDi $di Instance of Di 
         * @return void
         */
        public function __construct(IDi $di = null);

        /**
         * Method to construct instance of Entity
         *
         * @param array $params Params to Entity
         * @return FcPhp\Route\Interfaces\IEntity
         */
        public function getEntity(array $params = []) :IEntity;
    }
}
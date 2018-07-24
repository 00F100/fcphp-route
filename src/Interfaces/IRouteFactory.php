<?php

namespace FcPhp\Route\Interfaces
{
    interface IRouteFactory
    {
        public function getEntity(array $params = []) :IEntity;
    }
}
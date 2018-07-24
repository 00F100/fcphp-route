<?php

namespace FcPhp\Route\Interfaces
{
    interface IEntity
    {
        public function getMethod() :string;

        public function getRoute();

        public function getRule();

        public function getAction();

        public function getFilter() :array;

        public function getStatusCode();

        public function getStatusMessage();

        public function setFullRoute(string $fullRoute);

        public function getFullRoute();

        public function setParams(array $params);

        public function getParams() :array;
    }
}
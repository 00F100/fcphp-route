<?php

namespace FcPhp\Route
{
    use FcPhp\Route\Interfaces\IEntity;

    class Entity implements IEntity
    {
        private $method = 'GET';
        private $route;
        private $fullRoute;
        private $rule;
        private $action;
        private $statusCode;
        private $statusMessage;
        private $filter = [];

        public function __construct(array $params = [])
        {
            if(count($params) > 0) {
                foreach ($params as $index => $value) {
                    if(property_exists($this, $index)) {
                        $this->{$index} = $value;
                    }
                }
            }
        }

        public function getMethod() :string
        {
            return $this->method;
        }

        public function getRoute()
        {
            return $this->route;
        }

        public function getRule()
        {
            return $this->rule;
        }

        public function getAction()
        {
            return $this->action;
        }

        public function getFilter() :array
        {
            return $this->filter;
        }

        public function getStatusCode()
        {
            return $this->statusCode;
        }

        public function getStatusMessage()
        {
            return $this->statusMessage;
        }

        public function setFullRoute(string $fullRoute)
        {
            $this->fullRoute = $fullRoute;
        }

        public function getFullRoute()
        {
            return $this->fullRoute;
        }
    }
}
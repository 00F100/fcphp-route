<?php

namespace FcPhp\Route
{
    use FcPhp\Route\Interfaces\IEntity;

    class Entity implements IEntity
    {
        /**
         * @var string Method of request
         */
        private $method = 'GET';

        /**
         * @var string Route of request
         */
        private $route;

        /**
         * @var string Full route of request
         */
        private $fullRoute;

        /**
         * @var string Rule to access
         */
        private $rule;

        /**
         * @var string Action to execute
         */
        private $action;

        /**
         * @var int StatusCode of request
         */
        private $statusCode;

        /**
         * @var string Message of status
         */
        private $statusMessage;

        /**
         * @var array Filters to apply
         */
        private $filter = [];

        /**
         * @var array Params for send to controller
         */
        private $params = [];

        /**
         * Method to construct instance
         *
         * @param array $params Params to populate Entity
         * @return void
         */
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

        /**
         * Method to return method of request
         *
         * @return string
         */
        public function getMethod() :string
        {
            return $this->method;
        }

        /**
         * Method to return route of request
         *
         * @return string|null
         */
        public function getRoute()
        {
            return $this->route;
        }

        /**
         * Method to return rule to access
         *
         * @return string|null
         */
        public function getRule()
        {
            return $this->rule;
        }

        /**
         * Method to return action to execute
         *
         * @return string|null
         */
        public function getAction()
        {
            return $this->action;
        }

        /**
         * Method to return filters to apply
         *
         * @return array
         */
        public function getFilter() :array
        {
            return $this->filter;
        }

        /**
         * Method to return status code
         *
         * @return int
         */
        public function getStatusCode()
        {
            return $this->statusCode;
        }

        /**
         * Method to return status message
         *
         * @return string|null
         */
        public function getStatusMessage()
        {
            return $this->statusMessage;
        }

        /**
         * Method to configure full route
         *
         * @param string $fullRoute Full route
         * @return void
         */
        public function setFullRoute(string $fullRoute) :void
        {
            $this->fullRoute = $fullRoute;
        }

        /**
         * Method to return full route
         *
         * @return string|null
         */
        public function getFullRoute()
        {
            return $this->fullRoute;
        }

        /**
         * Method to configure params to controller
         *
         * @param array $params Params to controller
         * @return void
         */
        public function setParams(array $params) :void
        {
            $this->params = $params;
        }

        /**
         * Method to return params to controller
         *
         * @return array
         */
        public function getParams() :array
        {
            return $this->params;
        }
    }
}

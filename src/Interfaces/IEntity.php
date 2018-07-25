<?php

namespace FcPhp\Route\Interfaces
{
    interface IEntity
    {
        /**
         * Method to construct instance
         *
         * @param array $params Params to populate Entity
         * @return void
         */
        public function __construct(array $params = []);

        /**
         * Method to return method of request
         *
         * @return string
         */
        public function getMethod() :string;

        /**
         * Method to return route of request
         *
         * @return string|null
         */
        public function getRoute();

        /**
         * Method to return rule to access
         *
         * @return string|null
         */
        public function getRule();

        /**
         * Method to return action to execute
         *
         * @return string|null
         */
        public function getAction();

        /**
         * Method to return filters to apply
         *
         * @return array
         */
        public function getFilter() :array;

        /**
         * Method to return status code
         *
         * @return int
         */
        public function getStatusCode();

        /**
         * Method to return status message
         *
         * @return string|null
         */
        public function getStatusMessage();

        /**
         * Method to configure full route
         *
         * @param string $fullRoute Full route
         * @return void
         */
        public function setFullRoute(string $fullRoute) :void;

        /**
         * Method to return full route
         *
         * @return string|null
         */
        public function getFullRoute();

        /**
         * Method to configure params to controller
         *
         * @param array $params Params to controller
         * @return void
         */
        public function setParams(array $params) :void;

        /**
         * Method to return params to controller
         *
         * @return array
         */
        public function getParams() :array;
    }
}
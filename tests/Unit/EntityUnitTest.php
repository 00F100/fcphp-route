<?php

use FcPhp\Route\Entity;
// use FcPhp\Route\Interfaces\IRoute;
use FcPhp\Route\Interfaces\IEntity;
use PHPUnit\Framework\TestCase;

// use FcPhp\Route\Factories\RouteFactory;

class EntityUnitTest extends TestCase
{
    public function setUp()
    {
        $data = [
            'method' => 'GET',
            'route' => 'route/{id}',
            'fullRoute' => 'v1/route/{id}',
            'rule' => 'route-all',
            'action' => 'controller@method',
            'statusCode' => 200,
            'statusMessage' => 'success',
            'filter' => [
                'default' => 'method',
                'query' => [
                    'name' => 'raw'
                ]
            ],
            'params' => [
                10
            ],
        ];
        $this->instance = new Entity($data);
    }

    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof IEntity);
    }

    public function testMethod()
    {
        $this->assertEquals($this->instance->getMethod(), 'GET');
    }

    public function testRoute()
    {
        $this->assertEquals($this->instance->getRoute(), 'route/{id}');
    }

    public function testRule()
    {
        $this->assertEquals($this->instance->getRule(), 'route-all');
    }

    public function testAction()
    {
        $this->assertEquals($this->instance->getAction(), 'controller@method');
    }

    public function testFilter()
    {
        $this->assertEquals($this->instance->getFilter(), [
            'default' => 'method',
            'query' => [
                'name' => 'raw'
            ]
        ]);
    }

    public function testStatusCode()
    {
        $this->assertEquals($this->instance->getStatusCode(), 200);
    }

    public function testStatusMessage()
    {
        $this->assertEquals($this->instance->getStatusMessage(), 'success');
    }

    public function testFullRoute()
    {
        $this->instance->setFullRoute('full-route');
        $this->assertEquals($this->instance->getFullRoute(), 'full-route');
    }

    public function testParams()
    {
        $this->instance->setParams([20]);
        $this->assertEquals($this->instance->getParams(), [20]);
    }

}
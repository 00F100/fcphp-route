<?php

use FcPhp\Route\Route;
use FcPhp\Route\Interfaces\IRoute;
use FcPhp\Route\Interfaces\IEntity;
use PHPUnit\Framework\TestCase;

use FcPhp\Route\Factories\RouteFactory;

class RouteUnitTest extends TestCase
{
    public function setUp()
    {
        $this->entity = $this->createMock('FcPhp\SHttp\Interfaces\ISEntity');
        $this->autoload = $this->createMock('FcPhp\Autoload\Interfaces\IAutoload');
        $this->autoload
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValue(require 'tests/var/unit/config/routes.php'));

        $this->cache = $this->createMock('FcPhp\Cache\Interfaces\ICache');
        // $this->factory = $this->createMock('FcPhp\Route\Interfaces\IRouteFactory');
        $this->factory = new RouteFactory();

        // if(!is_dir('tests/var/unit/config')) {
        //     mkdir('tests/var/unit/config', 0755, true);
        // }

        $vendorPath = 'tests/*/*/config';

        $this->instance = new Route($this->entity, $this->autoload, $this->cache, $vendorPath, $this->factory);
    }

    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof IRoute);
    }

    public function testMatchRoute()
    {
        $match = $this->instance->match('GET', 'v1/users/10');
        $this->assertTrue($match instanceof IEntity);
        $this->assertEquals($match->getStatusCode(), 200);
    }

}
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
        $routes = require 'tests/var/unit/config/routes.php';
        $this->entity = $this->createMock('FcPhp\SHttp\Interfaces\ISEntity');
        $this->autoload = $this->createMock('FcPhp\Autoload\Interfaces\IAutoload');
        $this->autoload
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValue($routes));

        $this->cache = $this->createMock('FcPhp\Cache\Interfaces\ICache');
        $this->factory = $this->createMock('FcPhp\Route\Interfaces\IRouteFactory');
        $this->routeEntity = $this->createMock('FcPhp\Route\Interfaces\IEntity');
        $this->routeEntity
            ->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue(200));
        $this->routeEntity
            ->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue('GET'));
        $this->routeEntity
            ->expects($this->any())
            ->method('getRoute')
            ->will($this->returnValue('{parentCode}'));

        $this->factory
            ->expects($this->any())
            ->method('getEntity')
            ->will($this->returnValue($this->routeEntity));
        // $this->factory = new RouteFactory();

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

    public function testMatchRouteNonExists()
    {
        $routeEntity = $this->createMock('FcPhp\Route\Interfaces\IEntity');
        $routeEntity
            ->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue(404));
        $factory = $this->createMock('FcPhp\Route\Interfaces\IRouteFactory');
        $factory
            ->expects($this->any())
            ->method('getEntity')
            ->will($this->returnValue($routeEntity));

        $vendorPath = 'tests/*/*/config';

        $instance = new Route($this->entity, $this->autoload, $this->cache, $vendorPath, $factory);

        $match = $instance->match('GET', 'route/to/test/another/infor/mation');
        $this->assertTrue($match instanceof IEntity);
        $this->assertEquals($match->getStatusCode(), 404);
    }

}
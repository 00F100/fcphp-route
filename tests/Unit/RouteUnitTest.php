<?php

use FcPhp\Route\Route;
use FcPhp\Route\Interfaces\IRoute;
use FcPhp\Route\Interfaces\IEntity;
use PHPUnit\Framework\TestCase;

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

        $this->instance->callback('initCallback', function(array $routes) {
            $this->assertTrue(is_array($routes));
        });
        $this->instance->callback('matchCallback', function(array $routes, string $method, string $route, array $entity, IEntity $routeEntity) {
            $this->assertTrue(is_array($routes));
            $this->assertTrue(!empty($method));
            $this->assertTrue(!empty($route));
            $this->assertTrue(is_array($entity));
            $this->assertTrue($routeEntity instanceof IEntity);
        });
        $this->instance->callback('notFoundCallback', function(array $routes, string $method, string $route, array $entity = [], IEntity $routeEntity = null) {
            $this->assertTrue(is_array($routes));
            $this->assertTrue(!empty($method));
            $this->assertTrue(!empty($route));
            $this->assertTrue(is_array($entity));

        });
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

    public function testRouteNotFound()
    {
        $match = $this->instance->match('POST', 'tesssst');
        $this->assertEquals($match->getStatusCode(), 200);
    }



    public function testCustomRouteNotPermissionConstruct()
    {

        $entity = $this->createMock('FcPhp\SHttp\Interfaces\ISEntity');
        $entity
            ->expects($this->any())
            ->method('check')
            ->will($this->returnValue(false));

        $factory = $this->createMock('FcPhp\Route\Interfaces\IRouteFactory');
        $routeEntity = $this->createMock('FcPhp\Route\Interfaces\IEntity');
        $routeEntity
            ->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue(403));
        $routeEntity
            ->expects($this->any())
            ->method('getRule')
            ->will($this->returnValue('permission'));
        $routeEntity
            ->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue('POST'));
        $routeEntity
            ->expects($this->any())
            ->method('getRoute')
            ->will($this->returnValue('{parentCode}'));

        $factory
            ->expects($this->any())
            ->method('getEntity')
            ->will($this->returnValue($routeEntity));



        $vendorPath = 'tests/*/*/config';
        $instance = new Route($entity, $this->autoload, $this->cache, $vendorPath, $factory);
        $match = $instance->match('GET', 'v1/users/10');
        $this->assertTrue($match instanceof IEntity);
        $this->assertEquals($match->getStatusCode(), 403);
    }

}

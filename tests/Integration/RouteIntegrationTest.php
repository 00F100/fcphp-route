<?php

use FcPhp\SHttp\SEntity;
use FcPhp\Autoload\Autoload;
use FcPhp\Route\Route;
use FcPhp\Cache\Facades\CacheFacade;
use FcPhp\Route\Factories\RouteFactory;
use FcPhp\Route\Interfaces\IEntity;
use FcPhp\Route\Interfaces\IRoute;
use FcPhp\Di\Facades\DiFacade;
use PHPUnit\Framework\TestCase;
use FcPhp\Route\Facades\RouteFacade;

class RouteIntegrationTest extends TestCase
{
    public function setUp()
    {
        // $this->di = DiFacade::getInstance();
        $this->entity = new SEntity();
        // $this->autoload = new Autoload();

        // $this->cache = CacheFacade::getInstance('tests/var/cache');
        // $this->factory = new RouteFactory($this->di);

        $this->vendorPath = 'tests/*/*/config';

        // $this->instance = new Route($this->entity, $this->autoload, $this->cache, $this->vendorPath, $this->factory);
        $this->instance = RouteFacade::getInstance($this->entity, $this->vendorPath, 'tests/var/cache');


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

        $this->match = $this->instance->match('GET', 'v1/users/10');
    }

    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof IRoute);
    }

    public function testFactoryNonDi()
    {
        $entity = new SEntity();
        $autoload = new Autoload();

        $cache = CacheFacade::getInstance('tests/var/cache');
        $factory = new RouteFactory();

        $instance = new Route($entity, $autoload, $cache, $this->vendorPath, $factory);
        // $instance = RouteFacade::getInstance($entity, $this->vendorPath, 'tests/var/cache');
        $this->assertTrue($instance instanceof IRoute);
        $match = $instance->match('GET', 'v1/users/10');
        $this->assertEquals(200, $match->getStatusCode());
    }

    public function testMatchRoute()
    {
        $match = $this->instance->match('GET', 'v1/users/10');
        $this->assertTrue($match instanceof IEntity);
        $this->assertEquals($match->getStatusCode(), 200);
    }

    public function testMatchRouteNonExists()
    {

        $vendorPath = 'tests/var/unit/config';
        // $instance = new Route($this->entity, $this->autoload, $this->cache, $vendorPath, $this->factory);
        $instance = RouteFacade::getInstance($this->entity, $vendorPath, 'tests/var/cache');

        $match = $instance->match('GET', 'route/to/test/another/infor/mation');
        $this->assertTrue($match instanceof IEntity);
        $this->assertEquals($match->getStatusCode(), 404);
    }



    public function testMethod()
    {
        $this->assertEquals($this->match->getMethod(), 'GET');
    }

    public function testRoute()
    {
        $this->assertEquals($this->match->getRoute(), '{parentId}');
    }

    public function testRule()
    {
        $this->assertEquals($this->match->getRule(), null);
    }

    public function testAction()
    {
        $this->assertEquals($this->match->getAction(), 'Controller@getByParent');
    }

    public function testFilter()
    {
        $this->assertEquals($this->match->getFilter(), [
            'default' => 'escape',
            'body' => [
                'type' => 'xml',
                'content' => 'xmlparser'
            ],
            'query' => [
                'name' => 'raw',
            ]
        ]);
    }

    public function testStatusCode()
    {
        $this->assertEquals($this->match->getStatusCode(), 200);
    }

    public function testRouteNotFound()
    {
        $match = $this->instance->match('POST', 'tesssst');
        $this->assertEquals($match->getStatusCode(), 404);
    }

    public function testStatusMessage()
    {
        $this->assertEquals($this->match->getStatusMessage(), null);
    }

    public function testFullRoute()
    {
        $this->match->setFullRoute('full-route');
        $this->assertEquals($this->match->getFullRoute(), 'full-route');
    }

    public function testParams()
    {
        $this->match->setParams([20]);
        $this->assertEquals($this->match->getParams(), [20]);
    }

    public function testCustomRouteConstruct()
    {
        // $di = DiFacade::getInstance();
        $entity = new SEntity();
        // $autoload = new Autoload();

        // $cache = CacheFacade::getInstance('tests/var/cache');
        // $factory = new RouteFactory($di);
        $vendorPath = 'tests/*/*/config';
        // $instance = new Route($entity, $autoload, $cache, $vendorPath, $factory, false, [
        //     'v1/users' => [
        //         [
        //             'method' => 'POST',
        //             'route' => 'test',
        //             'statusCode' => 204
        //         ]
        //     ]
        // ]);
        $instance = RouteFacade::getInstance($entity, $vendorPath, 'tests/var/cache', [
            'v1/users' => [
                [
                    'method' => 'POST',
                    'route' => 'test',
                    'statusCode' => 204
                ]
            ]
        ]);
        $match = $instance->match('POST', 'v1/users/test');
        $this->assertTrue($match instanceof IEntity);
        $this->assertEquals($match->getStatusCode(), 204);
    }

    public function testCustomRouteNotPermissionConstruct()
    {
        $entity = new SEntity();
        $vendorPath = 'tests/*/*/config';
        $instance = RouteFacade::getInstance($entity, $vendorPath, 'tests/var/cache', [
            'v1/users' => [
                [
                    'method' => 'POST',
                    'route' => 'test',
                    'rule' => 'permission',
                    'statusCode' => 204
                ]
            ]
        ]);
        $match = $instance->match('POST', 'v1/users/test');
        $this->assertTrue($match instanceof IEntity);
        $this->assertEquals($match->getStatusCode(), 403);
    }

    public function testFactoryReturnEntityNonDi()
    {
        $entity = new SEntity();
        $autoload = new Autoload();

        $cache = CacheFacade::getInstance('tests/var/cache');
        $factory = new RouteFactory();
        $vendorPath = 'tests/*/*/config';
        $instance = new Route($entity, $autoload, $cache, $vendorPath, $factory);
        $match = $instance->match('GET', 'v1/users/10');
        $this->assertEquals($match->getRoute(), '{parentId}');
    }

}

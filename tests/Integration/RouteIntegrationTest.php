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

class RouteIntegrationTest extends TestCase
{
    public function setUp()
    {
        $this->di = DiFacade::getInstance();
        $this->entity = new SEntity();
        $this->autoload = new Autoload();

        $this->cache = CacheFacade::getInstance('tests/var/cache');
        $this->factory = new RouteFactory($this->di);

        $this->vendorPath = 'tests/*/*/config';

        $this->instance = new Route($this->entity, $this->autoload, $this->cache, $this->vendorPath, $this->factory);

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
        $this->assertTrue($instance instanceof IRoute);
        $match = $instance->match('GET', 'v1/users/10');
        $this->assertEquals($match->getStatusCode(), 200);
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
        $instance = new Route($this->entity, $this->autoload, $this->cache, $vendorPath, $this->factory);

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
            'query' => [
                'name' => 'raw'
            ]
        ]);
    }

    public function testStatusCode()
    {
        $this->assertEquals($this->match->getStatusCode(), 200);
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

}
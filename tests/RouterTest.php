<?php

namespace PHPRouter\Test;

use PHPRouter\RouteCollection;
use PHPRouter\Router;
use PHPRouter\Route;
use PHPUnit_Framework_TestCase;

class RouterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider matcherProvider
     */
    public function testMatch($router, $path, $expected)
    {
        $this->assertEquals($expected, !!$router->match($path));
    }

    public function testBasePathConfigIsSettedProperly()
    {
        $router =  new Router(new RouteCollection);
        $router->setBasePath('/webroot/');

        $this->assertAttributeEquals('/webroot', 'basePath', $router);
    }

    public function testMatchRouterUsingBasePath()
    {
        $collection = new RouteCollection();
        $collection->attach(new Route('/users/', array(
            '_controller' => 'PHPRouter\Test\SomeController::users_create',
            'methods' => 'GET'
        )));

        $router =  new Router($collection);
        $router->setBasePath('/localhost/webroot');

        $_SERVER = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/localhost/webroot/users/';
        $_SERVER['SCRIPT_NAME'] = 'index.php';

        $this->assertTrue((bool) $router->matchCurrentRequest());
    }

    private function getRouter()
    {
        $collection = new RouteCollection();
        $collection->attach(new Route('/users/', array(
            '_controller' => 'PHPRouter\Test\SomeController::users_create',
            'methods' => 'GET'
        )));
        $collection->attach(new Route('/user/:id', array(
            '_controller' => 'PHPRouter\Test\SomeController::user',
            'methods' => 'GET'
        )));
        $collection->attach(new Route('/', array(
            '_controller' => 'PHPRouter\Test\SomeController::indexAction',
            'methods' => 'GET'
        )));
        return new Router($collection);
    }

    public function matcherProvider1()
    {
        $router = $this->getRouter();
        return array(
            array($router, '', true),
            array($router, '/', true),
            array($router, '/aaa', false),
            array($router, '/users', true),
            array($router, '/user/1', true),
            array($router, '/user/%E3%81%82', true),
        );
    }

    public function matcherProvider2()
    {
        $router = $this->getRouter();
        $router->setBasePath('/api');
        return array(
            array($router, '', false),
            array($router, '/', false),
            array($router, '/aaa', false),
            array($router, '/users', false),
            array($router, '/user/1', false),
            array($router, '/user/%E3%81%82', false),

            array($router, '/api', true),
            array($router, '/api/aaa', false),
            array($router, '/api/users', true),
            array($router, '/api/user/1', true),
            array($router, '/api/user/%E3%81%82', true),
        );
    }

    public function matcherProvider()
    {
        return array_merge($this->matcherProvider1(), $this->matcherProvider2());
    }
}

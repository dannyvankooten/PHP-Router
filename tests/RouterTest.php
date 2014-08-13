<?php

use PHPRouter\RouteCollection;
use PHPRouter\Router;
use PHPRouter\Route;

class someController
{
    public function users_create() {}
    public function indexAction() {}
    public function user() {}
}

class RouterTest extends PHPUnit_Framework_TestCase
{
    private function getRouter()
    {
        $collection = new RouteCollection();
        $collection->attach(new Route('/users/', array(
            '_controller' => 'someController::users_create',
            'methods' => 'GET'
        )));
        $collection->attach(new Route('/user/:id', array(
            '_controller' => 'someController::user',
            'methods' => 'GET'
        )));
        $collection->attach(new Route('/', array(
            '_controller' => 'someController::indexAction',
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

    /**
     * @dataProvider matcherProvider
     */
    public function testMatch($router, $path, $expected)
    {
        $this->assertEquals($expected, !!$router->match($path));
    }
}

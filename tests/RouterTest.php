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

    public function matcherProvider()
    {
        $router = $this->getRouter();
        return array(
            array($router, '', '', true),
            array($router, '', '/', true),
            array($router, '', '/aaa', false),
            array($router, '', '/users', true),
            array($router, '', '/user/1', true),
            array($router, '', '/user/%E3%81%82', true),

            array($router, '/api', '', false),
            array($router, '/api', '/', false),
            array($router, '/api', '/aaa', false),
            array($router, '/api', '/users', false),
            array($router, '/api', '/user/1', false),
            array($router, '/api', '/user/%E3%81%82', false),

            array($router, '/api', '/api', true),
            array($router, '/api', '/api/aaa', false),
            array($router, '/api', '/api/users', true),
            array($router, '/api', '/api/user/1', true),
            array($router, '/api', '/api/user/%E3%81%82', true),
        );
    }

    /**
     * @dataProvider matcherProvider
     */
    public function testMatch($router, $base, $path, $expected)
    {
        $router->setBasePath($base);
        $this->assertEquals($expected, !!$router->match($path));
    }
}

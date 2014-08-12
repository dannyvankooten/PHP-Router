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
    public function testMatch()
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

        $router = new Router($collection);
        $this->assertFalse(false, $router->match('/aaa'));
        $this->assertNotEquals(false, $router->match('/users'));
        $this->assertNotEquals(false, $router->match('/user/1'));
        $this->assertNotEquals(false, $router->match('/user/%E3%81%82'));

        $router->setBasePath('/api');
        $this->assertFalse($router->match('/aaa'));
        $this->assertFalse($router->match('/users'));
        $this->assertFalse($router->match('/user/1'));
        $this->assertFalse(false, $router->match('/user/%E3%81%82'));

        $this->assertFalse($router->match('/api/aaa'));
        $this->assertNotEquals(false, $router->match('/api/users'));
        $this->assertNotEquals(false, $router->match('/api/user/1'));
        $this->assertNotEquals(false, $router->match('/api/user/%E3%81%82'));
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: Adam Jedro
 * Date: 2015-01-22
 */

namespace PHPRouter\Test;

require_once "SomeController.php";

use PHPRouter\Route;
use PHPRouter\RouteCollection;
use PHPRouter\Router;
use PHPUnit_Framework_TestCase;

class ReverseRouteTest extends PHPUnit_Framework_TestCase{

	private $router;

	protected function setUp()
	{
		$collection = new RouteCollection();

		$collection->attach(new Route('/users', array(
					'_controller' => 'PHPRouter\Test\SomeController::users_create',
					'methods' => 'GET',
					'name' => 'users'
				)));
		$collection->attach(new Route('/user/:id', array(
					'_controller' => 'PHPRouter\Test\SomeController::users_create',
					'methods' => 'GET',
					'name' => 'user'
				)));

		$this->router = new Router($collection);
	}


	public function testReverseUrl()
	{
		$this->assertEquals('/user/10',
			$this->router->generate('user', array('id' => 10)) );
	}

	public function testReverseUrlWithParams()
	{
		$this->assertEquals('/users',
			$this->router->generate('users') );
	}

} 
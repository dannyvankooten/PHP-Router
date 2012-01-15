<?php
//require Router class
require 'Router.php';
require 'Route.php';

$router = new Router();

$router->setBasePath('/rest-router');

// maps / to controller 'users' and method 'index'.
$router->map('/', 'users#index', array('methods' => 'GET'));
$router->map('/users/:id/edit/', 'users#edit', array('methods' => 'GET', 'name' => 'user_edit_page'));

$params = $router->matchCurrentRequest();
var_dump($params);

// maps /user/5 to controller 'users', method 'show' with parameter 'id' => 5
// this route won't match /users/i5 because of the filter regex.
#$r->match('/users/:id', 'users#show', array('filters' => array('id' => '(\d+)')));

// maps POST request to /users/ to controller 'users' and method 'create'
#$r->match('/users', 'users#create', array('via' => 'post'));

// maps /photos/show to controller 'photos' and method 'show'
#$r->match('/photos/show');

// maps GET /users/5/edit to controller 'users', method 'edit' with parameters 'id' => 5 and saves route as a named route.

?><h3>Reversed routing</h3><?php 
// echoes /users/5/edit
echo "Route for user_edit_page with ID 5: ". $router->reverse('user_edit_page', array('id' => '5'));
<?php
require 'Router.php';
require 'Route.php';

$router = new Router();

$router->setBasePath('/rest-router');

// maps / to controller 'users' and method 'index'.
$router->map('/', 'users#index', array('methods' => 'GET'));
$router->map('/users/:id/edit/', 'users#edit', array('methods' => 'GET', 'name' => 'user_edit_page'));

$params = $router->matchCurrentRequest();
var_dump($params);

?><h3>Reversed routing</h3><?php 
// echoes /users/5/edit
echo "Route for user_edit_page with ID 5: ". $router->reverse('user_edit_page', array('id' => '5'));
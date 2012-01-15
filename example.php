<?php
//require Router class
require 'Router.php';
require 'Route.php';

// construct Router instance, specifying the base request url as a parameter.
$r = new Router();

$r->setBasePath('/rest-router');

// maps / to controller 'users' and method 'index'.
$r->map('/', 'users#index', array('methods' => 'GET'));

$r->map('/users/:id/edit', 'users#edit', array('methods' => 'GET', 'name' => 'user_edit_page'));

$r->execute();

// maps /user/5 to controller 'users', method 'show' with parameter 'id' => 5
// this route won't match /users/i5 because of the filter regex.
#$r->match('/users/:id', 'users#show', array('filters' => array('id' => '(\d+)')));

// maps POST request to /users/ to controller 'users' and method 'create'
#$r->match('/users', 'users#create', array('via' => 'post'));

// maps /photos/show to controller 'photos' and method 'show'
#$r->match('/photos/show');

// maps GET /users/5/edit to controller 'users', method 'edit' with parameters 'id' => 5 and saves route as a named route.


if ($r->isRouteMatched()) {
    ?>
    <h1>Route found!</h1>
    <p><b>Target: </b><?php var_dump($r->targetController); ?></p>
    <p><b>Params: </b><?php var_dump($r->getArguments()); ?></p>
    <?php
} else {
    ?><h1>No route found.</h1><?php
}

?><h3>Reversed routing</h3><?php 
// echoes /users/5/edit
echo "Route for user_edit_page with ID 5: ". $r->reverse('user_edit_page', array('id' => '5'));
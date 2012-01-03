<?php
//require Router class
require 'Router.php';

// construct Router instance, specifying the base request url as a parameter.
$r = new Router('/rest-router');

// maps / to controller 'users' and method 'index'.
#$r->match('/', 'users#index');

// maps /user/5 to controller 'users', method 'show' with parameter 'id' => 5
// this route won't match /users/i5 because of the filter regex.
$r->match('/users/:id', 'users#show', array('filters' => array('id' => '(\d+)')));

// maps POST request to /users/ to controller 'users' and method 'create'
#$r->match('/users', 'users#create', array('via' => 'post'));

// maps /photos/show to controller 'photos' and method 'show'
#$r->match('/photos/show');

// maps GET /users/5/edit to controller 'users', method 'edit' with parameters 'id' => 5 and saves route as a named route.
#$r->match('/users/:id/edit', 'users#edit', array('via' => 'get', 'as' => 'user_edit_page'));


// maps multiple routes
// GET /users will map to users#index
// GET /users/5 will map to users#show
#$r->resources('users', array('only' => 'index,show'));

if ($r->hasRoute()) {
    extract($r->getRoute());
    ?>
    <h1>Route found!</h1>
    <p><b>Controller: </b><?php echo $controller; ?></p>
    <p><b>Action: </b><?php echo $action; ?></p>
    <p><b>Params: </b><?php var_dump($params); ?></p>
    <?php
} else {
    ?><h1>No route found.</h1><?php
}

?><h3>Reversed routing</h3><?php 
// echoes /users/5/edit
#echo "Route for user_edit_page with ID 5: ". $r->reverse('user_edit_page', array('id' => '5'));
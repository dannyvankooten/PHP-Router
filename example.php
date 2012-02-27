<?php
require 'Router.php';
require 'Route.php';

$router = new Router();
$router->setBasePath('/PHP-Router');

$router->map('/', 'someController:indexAction', array('methods' => 'GET'));
$router->map('/users/:id/edit/', 'users#edit', array('methods' => 'GET', 'name' => 'users_edit'));
$router->map('/contact/',array('controller' => 'someController', 'action' => 'contactAction'), array('name' => 'contact'));
$router->map('/users/','users#create', array('methods' => 'POST', 'name' => 'users_create'));
$router->map('/users/','users#list', array('methods' => 'GET', 'name' => 'users_list'));
$router->map('/path-with-hyphens', 'some#target');

$route = $router->matchCurrentRequest();

?><h3>Current URL & HTTP method would route to: </h3>
<?php if($route) { ?>
	<pre><?php var_dump($route->getTarget()); ?></pre>
	<pre><?php var_dump($route->getParameters()); ?></pre>
<?php } else { ?>
	<pre>No route matched.</pre>
<?php } ?>

<h3>Try out these URL's.</h3>
<p><a href="<?php echo $router->generate('users_edit', array('id' => 5)); ?>"><?php echo $router->generate('users_edit', array('id' => 5)); ?></a></p>
<p><a href="<?php echo $router->generate('contact'); ?>"><?php echo $router->generate('contact'); ?></a></p>
<p><form action="" method="POST"><input type="submit" value="Post request to current URL" /></form></p>
<p><form action="<?php echo $router->generate('users_create'); ?>" method="POST"><input type="submit" value="POST request to <?php echo $router->generate('users_create'); ?>" /></form></p>
<p><a href="<?php echo $router->generate('users_list'); ?>">GET request to <?php echo $router->generate('users_list'); ?></p>
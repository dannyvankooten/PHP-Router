<?php
//require __DIR__.'/vendor/autoload.php';
require 'src\PHPRouter\RouteCollection.php';
require 'src\PHPRouter\Router.php';
require 'src\PHPRouter\Route.php';

use PHPRouter\RouteCollection;
use PHPRouter\Router;
use PHPRouter\Route;

$collection = new RouteCollection();
$collection->add('users', new Route('/users/', array(
    '_controller' => 'someController::users_create',
    'methods' => 'GET'
)));

$collection->add('index', new Route('/', array(
    '_controller' => 'someController::indexAction',
    'methods' => 'GET'
)));

$router = new Router($collection);
$router->setBasePath('/PHP-Router');
$route = $router->matchCurrentRequest();

var_dump($route);

?><h3>Current URL & HTTP method would route to: </h3>
<?php if ($route) { ?>
	<strong>Target:</strong>
	<pre><?php var_dump($route->getTarget()); ?></pre>

	<strong>Parameters:</strong>
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
<?php
require '../Router.php';
require '../Route.php';

$router = Router::getInstance();

// We set the basepath
$router->setBasePath('/AS-Router/examples');

// We map some urls
$router->map('/',                   array('target' => array('pages', 'index')));
$router->map('/contact/',           array('target' => array('pages', 'view'), 'params' => array('page' => 'contact')));
$router->map('/users/',             array('target' => array('users', 'create'), 'methods' => 'POST'));
$router->map('/users/:id/edit/',    array('target' => array('users', 'edit')));
$router->map('/blog/:slug',         array('target' => array('blog', 'show')));
$router->map('/site-section/:path', array('target' => array('pages', 'site'), 'filters' => array('path' => '(.*)')));
$router->map('/parts/:action',      array('target' => array('parts')));

// We match the current request

?><h3>Current URL & HTTP method would route to: </h3><?php
try {
$route = $router->matchCurrentRequest();?>
    <strong>Route</strong>
    <pre><?php var_dump($route); ?></pre>

    <strong>Target:</strong>
    <pre><?php var_dump($route->getTarget()); ?></pre>

    <strong>Parameters:</strong>
    <pre><?php var_dump($route->getParameters()); ?></pre><?php
} catch (Exception $e) { ?>
    <strong>Could not route request:</strong>
    <pre><?php var_dump($e); ?></pre><?php
}?>
<h3>Try out these URLs.</h3>
<p><form action="" method="POST"><input type="submit" value="Post request to current URL" /></form></p>
<p><a href="<?php echo $router->generate(array('users', 'edit'), array('id' => 5)); ?>"><?php echo $router->generate(array('users', 'edit'), array('id' => 5)); ?></a></p>
<p><a href="<?php echo $router->generate(array('pages', 'view'), array('page' => 'contact')); ?>"><?php echo $router->generate(array('pages', 'view'), array('page' => 'contact')); ?></a></p>
<p><form action="<?php echo $router->generate(array('users', 'create')); ?>" method="POST"><input type="submit" value="POST request to <?php echo $router->generate(array('users', 'create')); ?>" /></form></p>
<p><a href="<?php echo $router->generate(array('parts', time())); ?>">GET request to random action <?php echo $router->generate(array('parts', time())); ?></p>
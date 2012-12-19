AS Router class
================

A simple Rails inspired PHP router class.

* Usage of different HTTP Methods
* REST / Resourceful routing
* Reversed routing
* Dynamic URL's: use URL segments as parameters.

Info
----

AS Router is a fork of the [PHP Router](//github.com/dannyvankooten/PHP-Router) class by Danny van Kooten.

It was incorporated in AfroSoft's custom website framework and modified to fit our needs. We are releasing it back under the same license (MIT).

Usage
-----

***NOTE: The following usage is incorrect. New usage will be written and added.***

    <?php
    require 'Router.php';
    require 'Route.php';

    $router = new Router();

    $router->setBasePath('/PHP-Router');

    // defining routes can be as simple as this
    $router->map('/', 'users#index');

    // or somewhat more complicated
    $router->map('/users/:id/edit/', array('controller' => 'SomeController', 'action' => 'someAction'), array('methods' => 'GET,PUT', 'name' => 'users_edit', 'filters' => array('id' => '(\d+)')));

    // You can even specify closures as the Route's target
    $router->map('/hello/:name', function($name) { echo "Hello $name."; });

    // match current request URL & http method
    $target = $router->matchCurrentRequest();
    var_dump($target);

    // generate an URL 
    $router->generate('users_edit', array('id' => 5));

More Information
----------------

There is more information about the functions are capabilites in the documentation included with the code (phpdoc).

License
-------

MIT Licensed, http://www.opensource.org/licenses/MIT

You should have gotten a copy of the license in the file LICENSE.
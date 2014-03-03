# PHP Router class

A simple Rails inspired PHP router class.

* Usage of different HTTP Methods
* REST / Resourceful routing
* Reversed routing using named routes
* Dynamic URL's: use URL segments as parameters.

# Easy to install with **composer**

```javascript
{
    "require": {
        "dannyvankooten/php-router": "dev-master"
    }
}
```

## Usage
```php
<?php
require __DIR__.'/vendor/autoload.php';

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
```

## Load routers from a `yaml` file

We can define in a `yaml` file all the routes of our application. This facilitates our life when we need to *migrate*, *modify*, or later *add* new routes.

The route definition should follow the example below:

```yaml
base_path: /blog

routes:
  index: [/index, someClass.indexAction, GET]
  contact: [/contact, someClass.contactAction, GET]
  about: [/about, someClass.aboutAction, GET]
```
In our **Front Controller** would have something like:

```php
<?php
require __DIR__.'/vendor/autoload.php';

use PHPRouter\RouteCollection;
use PHPRouter\Config;
use PHPRouter\Router;
use PHPRouter\Route;

$config = Config::loadFromFile(__DIR__.'/router.yaml');
$router = Router::parseConfig($config);
$router->matchCurrentRequest();
```

## More information
Have a look at the example.php file or read trough the class' documentation for a better understanding on how to use this class.

If you like PHP Router you might also like [AltoRouter](//github.com/dannyvankooten/AltoRouter).

## License
MIT Licensed, http://www.opensource.org/licenses/MIT
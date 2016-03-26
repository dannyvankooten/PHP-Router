# PHP Router class

[![Latest Stable Version](https://poser.pugx.org/dannyvankooten/php-router/v/stable)](https://packagist.org/packages/dannyvankooten/php-router) 
[![Total Downloads](https://poser.pugx.org/dannyvankooten/php-router/downloads)](https://packagist.org/packages/dannyvankooten/php-router) 
[![Latest Unstable Version](https://poser.pugx.org/dannyvankooten/php-router/v/unstable)](https://packagist.org/packages/dannyvankooten/php-router) 
[![License](https://poser.pugx.org/dannyvankooten/php-router/license)](https://packagist.org/packages/dannyvankooten/php-router)

A simple Rails inspired PHP router class.

* Usage of different HTTP Methods
* REST / Resourceful routing
* Reversed routing using named routes
* Dynamic URL's: use URL segments as parameters.

# Authors

- [Danny van Kooten](https://github.com/dannyvankooten)
- [Jefersson Nathan](https://github.com/malukenho)

# Easy to install with **composer**

```sh
$ composer require dannyvankooten/php-router
```

## Usage

### Friendly URL

Create a simple .htaccess file on your root directory if you're using Apache with mod_rewrite enabled.

```apache
Options +FollowSymLinks
RewriteEngine On
RewriteRule ^(.*)$ index.php [NC,L]
```

If you're using nginx, setup your server section as following:

```nginx
server {
	listen 80;
	server_name mydevsite.dev;
	root /var/www/mydevsite/public;

	index index.php;

	location / {
		try_files $uri $uri/ /index.php?$query_string;
	}

	location ~ \.php$ {
		fastcgi_split_path_info ^(.+\.php)(/.+)$;
		# NOTE: You should have "cgi.fix_pathinfo = 0;" in php.ini

		# With php5-fpm:
		fastcgi_pass unix:/var/run/php5-fpm.sock;
		fastcgi_index index.php;
		include fastcgi.conf;
		fastcgi_intercept_errors on;
	}
}
```

This is a simple example of routers in action

```php
<?php
require __DIR__.'/vendor/autoload.php';

use PHPRouter\RouteCollection;
use PHPRouter\Router;
use PHPRouter\Route;

$collection = new RouteCollection();
$collection->attachRoute(new Route('/users/', array(
    '_controller' => 'someController::usersCreate',
    'methods' => 'GET'
)));

$collection->attachRoute(new Route('/', array(
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

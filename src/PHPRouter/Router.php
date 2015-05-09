<?php
/**
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */
namespace PHPRouter;

use Exception;
use PHPRouter\RouteCollection;

/**
 * Routing class to match request URL's against given routes and map them to a controller action.
 */
class Router
{
    /**
     * Array that holds all Route objects
     *
     * @var array
     */
    private $routes = array();

    /**
     * Array to store named routes in, used for reverse routing.
     * @var array
     */
    private $namedRoutes = array();

    /**
     * The base REQUEST_URI. Gets prepended to all route url's.
     * @var string
     */
    private $basePath = '';

    /**
     * @param RouteCollection $collection
     */
    public function __construct(RouteCollection $collection)
    {
        $this->routes = $collection;
    }

    /**
     * Set the base _url - gets prepended to all route _url's.
     * @param $basePath
     */
    public function setBasePath($basePath)
    {
        $this->basePath = (string) $basePath;
    }

    /**
     * Matches the current request against mapped routes
     */
    public function matchCurrentRequest()
    {
        $requestMethod = (
            isset($_POST['_method'])
            && ($_method = strtoupper($_POST['_method']))
            && in_array($_method, array('PUT', 'DELETE'))
        ) ? $_method : $_SERVER['REQUEST_METHOD'];

        $requestUrl = $_SERVER['REQUEST_URI'];

        // strip GET variables from URL
        if (($pos = strpos($requestUrl, '?')) !== false) {
            $requestUrl =  substr($requestUrl, 0, $pos);
        }

        return $this->match($requestUrl, $requestMethod);
    }

    /**
     * Match given request _url and request method and see if a route has been defined for it
     * If so, return route's target
     * If called multiple times
     *
     * @param string $requestUrl
     * @param string $requestMethod
     *
     * @return bool|Route
     */
    public function match($requestUrl, $requestMethod = 'GET')
    {
        foreach ($this->routes->all() as $routes) {

            // compare server request method with route's allowed http methods
            if (! in_array($requestMethod, (array) $routes->getMethods())) {
                continue;
            }

            $currentDir = dirname($_SERVER['SCRIPT_NAME']);
            $requestUrl = str_replace($currentDir, '', $requestUrl);

            // check if request _url matches route regex. if not, return false.
            if (! preg_match("@^" . $this->basePath . $routes->getRegex() . "*$@i", $requestUrl, $matches)) {
                continue;
            }

            $params = array();

            if (preg_match_all("/:([\w-%]+)/", $routes->getUrl(), $argument_keys)) {

                // grab array with matches
                $argument_keys = $argument_keys[1];

                // loop trough parameter names, store matching value in $params array
                foreach ($argument_keys as $key => $name) {
                    if (isset($matches[$key + 1])) {
                        $params[$name] = $matches[$key + 1];
                    }
                }

            }

            $routes->setParameters($params);
            $routes->dispatch();

            return $routes;
        }

        return false;
    }

    /**
     * Reverse route a named route
     *
     * @param $routeName
     * @param array $params Optional array of parameters to use in URL
     *
     * @throws Exception
     *
     * @return string The url to the route
     */
    public function generate($routeName, array $params = array())
    {
        // Check if route exists
        if (! isset($this->namedRoutes[$routeName])) {
            throw new Exception("No route with the name $routeName has been found.");
        }

        /** @var \PHPRouter\Route $route */
        $route = $this->namedRoutes[$routeName];
        $url = $route->getUrl();

        // replace route url with given parameters
        if ($params && preg_match_all("/:(\w+)/", $url, $param_keys)) {
            // grab array with matches
            $param_keys = $param_keys[1];

            // loop trough parameter names, store matching value in $params array
            foreach ($param_keys as $key) {
                if (isset($params[$key])) {
                    $url = preg_replace("/:(\w+)/", $params[$key], $url, 1);
                }
            }
        }

        return $url;
    }

    /**
     * Create routes by array, and return a Router object
     *
     * @param array $config provide by Config::loadFromFile()
     * @return Router
     */
    public static function parseConfig(array $config)
    {
        $collection = new RouteCollection();
        foreach ($config['routes'] as $name => $route) {
            $collection->attachRoute(new Route($route[0], array(
                '_controller' => str_replace('.', '::', $route[1]),
                'methods' => $route[2]
            )));
        }

        $router = new Router($collection);
        if (isset($config['base_path'])) {
            $router->setBasePath($config['base_path']);
        }

        return $router;
    }
}

<?php

/**
 * GetValue function.
 * 
 * Returns the value of the specified key or default is the key is not defined.
 * 
 * @param array $array The array to search.
 * @param string|int $key The key to retrieve.
 * @param mixed $default The default value of the key.
 * @return mixed The default value if the key is not set, the value of the key 
 * otherwise.
 */
function getValue(array $array, $key, $default = '') {
    return (isset($array[$key])) ? $array[$key] : $default;
}

/**
 * Router class.
 * 
 * Generates routes from given parameters and stores them. Can match a request 
 * to its associated route, returning that route object. Can generate an URL 
 * from given parameters if the parameters have been associated with a given 
 * route.
 * 
 * @package AS-Router
 * @license MIT
 */
class Router {

    /**
     * Array that holds all Route objects.
     * 
     * @var array
     */
    private $routes = array();

    /**
     * The base url.
     * 
     * @var string
     */
    private $basePath = '';

    /**
     * The instance of the class.
     * 
     * @var Router
     */
    private static $instance;

    /**
     * The current matched route.
     * 
     * @var Route
     */
    public $currentRoute;

    /**
     * Router constructor.
     */
    private function __construct() {
        
    }

    /**
     * Clone magic function.
     */
    private function __clone() {
        
    }

    /**
     * GetInstance method.
     * 
     * Returns current instance of the Router object
     * 
     * @return Router 
     */
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new Router();
        }
        return self::$instance;
    }

    /**
     * SetBasePath method.
     * 
     * Sets the base url that will get prepended to all route urls.
     * 
     * @param string $basePath
     */
    public function setBasePath($basePath) {
        $this->basePath = (string) $basePath;
    }

    /**
     * GetBasePath method.
     * 
     * Returns the base url.
     * 
     * @return string
     */
    public function getBasePath() {
        return $this->basePath;
    }

    /**
     * PrefixURL method.
     * 
     * Prefixes url by the Router::$basePath.
     * 
     * @param string $url
     * @return string 
     */
    public function prefixURL($url) {
        return $this->basePath . $url;
    }

    /**
     * Route factory method.
     * 
     * Registers a route matching the given URL. The optionals arguments are:
     * 
     * - `target`: an array specifying the controller and action.
     * - `methods`: the HTTP methods allowed by the route. Defaults to `GET`.
     * - `filters`: custom regexes matching named parameters in the URL. Named 
     * parameters with no matching filter will default to `([\w-]+)`.
     * - `params`: pre-set the value of the route's parameters.
     * - `name`: the name of the route. REQUIRED.
     * 
     * The magic named parameters `:controller` and `:action` will set the route's 
     * target to their value, regardless of the previous target value.
     * 
     * @param string $routeUrl string
     * @param array $args Array of optional arguments.
     */
    public function map($routeUrl, array $args = array()) {
        if (is_array($routeUrl)) {
            foreach ($routeUrl as $key => $value)
                $this->map($key, $value);
        } else {
            $route = new Route();
            $route->setUrl($routeUrl);

            if (isset($args['target']))
                $route->setTarget($args['target']);

            if (isset($args['methods'])) {
                $methods = (is_array($args['methods'])) ? $args['methods'] : explode('|', $args['methods']);
                $route->setMethods($methods);
            }

            if (isset($args['filters']))
                $route->setFilters($args['filters']);

            if (isset($args['params']))
                $route->setParameters($args['params']);

            $this->routes[] = $route;
        }
    }

    /**
     * Matches the current request against mapped routes.
     */
    public function matchCurrentRequest() {
        $requestMethod = (isset($_POST['_method']) && ($_method = strtoupper($_POST['_method'])) && in_array($_method, array('PUT', 'DELETE'))) ? $_method : $_SERVER['REQUEST_METHOD'];
        $requestUrl = $_SERVER['REQUEST_URI'];

        // strip GET variables from URL
        if (($pos = strpos($requestUrl, '?')) !== false) {
            $requestUrl = substr($requestUrl, 0, $pos);
        }
        return $this->match($requestUrl, $requestMethod);
    }

    /**
     * Match given request url and request method and see if a route has been 
     * defined for it.
     * If so, return route's target.
     * If not, try to extract a controller and target.
     * @param string $requestUrl
     * @param string $requestMethod
     * @return Route
     * @throws RoutingException 
     */
    private function match($requestUrl, $requestMethod = 'GET') {
        $cleanUrl = str_replace($this->getBasePath(), '', $requestUrl);
        foreach ($this->routes as $route) {
            // compare server request method with route's allowed http methods
            if (!in_array($requestMethod, $route->getMethods()))
                continue;

            // check if request url matches route regex. if not, return false.
            if (!preg_match("@^" . $route->getRegex() . "*$@i", $cleanUrl, $matches))
                continue;

            $this->currentRoute = clone $route;

            $params = $this->currentRoute->getParameters();

            if (preg_match_all("@:([\w-]+)@", $this->currentRoute->getUrl(), $argument_keys)) {
                // grab array with matches
                $argument_keys = $argument_keys[1];

                // loop trough parameter names, store matching value in $params array
                foreach ($argument_keys as $key => $name) {
                    if (isset($matches[$key + 1]))
                        $params[$name] = $matches[$key + 1];
                }
            }

            $target = $this->currentRoute->getTarget();
            if (isset($params['controller'])) {
                $target['controller'] = ucfirst(strtolower($params['controller']));
                unset($params['controller']);
            }
            if (isset($params['action'])) {
                $target['action'] = $params['action'];
                unset($params['action']);
            }
            $this->currentRoute->setTarget($target);

            $params['requestMethod'] = $requestMethod;
            $params['requestURL'] = $requestUrl;
            $params['cleanURL'] = $cleanUrl;
            $this->currentRoute->setParameters($params);
            return $this->currentRoute;
        }
        throw new RoutingException("No route matching $requestMethod $requestUrl has been found.");
    }

    /**
     * URL generation method.
     * 
     * Generates a URL from the given target.
     * 
     * @param array $target The target to generate.
     * @param array $params Optional array of parameters to use in URL
     * @return string The url to the route
     * @throws UrlException 
     */
    public function generate(array $rawTarget, array $params = array()) {
        $target = array(
            'controller' => getValue($rawTarget, 'controller', null),
            'action' => getValue($rawTarget, 'action', null)
        );
        if (is_indexed($rawTarget)) {
            if (2 == count($rawTarget)) {
                $target['controller'] = $rawTarget[0];
                $target['action'] = $rawTarget[1];
            } elseif (1 == count($rawTarget))
                $target['action'] = $rawTarget[0];
        }
        // Check that controller is complete
        if (!isset($target['controller']) || empty($target['controller']) || null === $target['controller']) {
            if ($this->currentRoute instanceof Route) {
                $currentTarget = $this->currentRoute->getTarget();
                if (!empty($currentTarget['controller']))
                    $target['controller'] = $currentTarget['controller'];
                else
                    throw new UrlException('Incomplete target was given for route generation (Current route has no controller).');
            } else
                throw new UrlException('Incomplete target was given for route generation (No current route).');
        }

        if (!isset($target['action']))
            $target['action'] = 'index';

        foreach ($this->routes as $route) {
            if (!$route->match($target, $params))
                continue;

            $url = $route->getUrl();
            $param_keys = array();

            // replace route url with given parameters
            if (0 < preg_match_all("@:(\w+)@", $url, $param_keys)) {
                // grab array with matches
                $param_keys = $param_keys[1];
                // loop trough parameter names, store matching value in $params array
                foreach ($param_keys as $i => $key) {
                    switch ($key) {
                        case 'controller':
                        case 'action':
                            $url = str_replace(':' . $key, $target[$key], $url);
                            break;
                        default:
                            $url = str_replace(':' . $key, $params[$key], $url);
                            break;
                    }
                }
            }
            return $this->prefixURL($url);
        }

        throw new UrlException("No route matching {$target['controller']}#{$target['action']}(" . urldecode(http_build_query($params)) . ") has been found.");
    }

}

/**
 * Routing Exception
 * 
 * Exception thrown when we can't match the current request to a route.
 *
 * @package AS-Router
 * @license MIT
 */
class RoutingException extends Exception {}

/**
 * Url Exception
 *
 * Exception thrown when we can't generate a url with the given information.
 *
 * @package AS-Router
 * @license MIT
 */
class UrlException extends Exception {}


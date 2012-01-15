<?php

/**
 * Routing class to match request URL's against given routes and map them to a controller action.
 *
 * @author Danny
 */
class Router {

    private $routes = array();

    /**
     * Array to store named routes in, used for reverse routing.
     * @var array 
     */
    private $namedRoutes = array();

    /**
     * Boolean whether a route has been matched.
     * @var boolean
     */
    private $routeMatched = false;

    /**
     * The matched route. Contains an array with controller, action and optional parameter values.
     * @var array 
     */
    private $matchedRoute = array();

    /**
     * The base REQUEST_URI. Gets prepended to all route url's.
     * @var string
     */
    private $basePath = '';
    
    /**
    * Temporary variable to store route arguments for usage inside the regex callback
    * @var array
    */
    private $arguments = array();

    /**
     * Set the base url - gets prepended to all route url's.
     * @param string $base_url 
     */
    public function setBasePath($basePath) {
        $this->basePath = $basePath;
    }

    /**
     * Has a route been matched?
     * @return boolean True if a route has been found, false if not. 
     */
    public function isRouteMatched() {
        return $this->routeMatched;
    }

    public function getArguments() {
        return $this->arguments;
    }

    /**
     * Get array with data of the matched route.
     * @return array Array containing the controller, action and parameters of matched route. 
     */
    public function getMatchedRoute() {
        return $this->matchedRoute;
    }

    public function addRoute(Route $route) {
        $this->routes[] = $route;
    }

    public function map($routeUrl, $target = '', array $args = array()) {
        $route = new Route();

        $route->setUrl($this->basePath . $routeUrl);

        $route->setTarget($target);

        if(isset($args['methods'])) {
            $methods = explode(',', $args['methods']);
            $route->setMethods($methods);
        }

        if(isset($args['filters'])) {
            $route->setFilters($args['filters']);
        }

        if(isset($args['name'])) {
            $route->setName($args['name']);
        }

        $this->routes[] = $route;
    }

    public function execute() {

        $requestMethod = (isset($_POST['_method']) && ($_method = strtoupper($_POST['_method'])) && in_array($_method,array('PUT','DELETE'))) ? $_method : $_SERVER['REQUEST_METHOD'];

        $requestUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestUrl = rtrim($requestUrl, '/');
                
        foreach($this->routes as $route) {
            
            // if route has been given a name, store it in the namedRoutes array
            if($route->getName() !== null) { $this->namedRoutes[$route->getName()] = $route; }

            // don't do anything if a route has already been found
            if($this->routeMatched) continue;

            // compare server request method with route's allowed http methods
            if(!in_array($requestMethod, $route->getMethods())) continue;

            // check if request url matches route regex. if not, return false.
            if (!preg_match("@^".$route->getRegex()."*$@i", $requestUrl, $matches)) continue;

            if (preg_match_all("/:(\w+)/", $route->getUrl(), $argument_keys)) {

                // grab array with matches
                $argument_keys = $argument_keys[1];

                // loop trough parameter names, store matching value in $params array
                foreach ($argument_keys as $key => $name) {
                    if (isset($matches[$key + 1]))
                        $this->arguments[$name] = $matches[$key + 1];
                }

            }

            if ($route->getTarget() !== null) {
                // route has an explicit target
                $target = explode('#', $route->getTarget());
            } else {
                // route has no explicit target, extract it from the request URL
                $target = explode('/', ltrim(str_replace($this->basePath, '', $requestUrl), '/')); 
            }

            $this->targetController = $target[0];
            $this->targetAction = (isset($target[1])) ? $target[1] : 'index';

            $this->routeMatched = true;
            $this->matchedRoute = $route;

        }

    }

    /**
     * Match a route to the current REQUEST_URI. Returns true on succes (route matches), false on failure.
     * 
     * @param string $route_url The URL of the route to match, must start with a leading slash. Dynamic URL value's must start with a colon. 
     * @param string $target The controller and action to map this route_url to, seperated by a hash (#). The action value defaults to 'index'. (optional)
     * @param array $args Accepts two keys, 'via' and 'as'. 'via' accepts a comma seperated list of HTTP Methods for this route. 'as' accepts a string and will be used as the name of this route.
     * @return boolean True if route matches URL, false if not.
     */
    
    /**
     * Reverse route a named route
     * 
     * @param string $route_name The name of the route to reverse route.
     * @param array $params Optional array of parameters to use in URL
     * @return string The url to the route
     */
    public function reverse($routeName, array $params = array()) {
        // Check if route exists
        if (!isset($this->namedRoutes[$routeName]))
            throw new Exception("No route with the name $routeName has been found.");

        $route = $this->namedRoutes[$routeName];
        $url = $route->getUrl();

        // replace route url with given parameters
        if ($params && preg_match_all("/:(\w+)/", $url, $param_keys)) {

            // grab array with matches
            $param_keys = $param_keys[1];

            // loop trough parameter names, store matching value in $params array
            foreach ($param_keys as $i => $key) {
                if (isset($params[$key]))
                    $url = preg_replace("/:(\w+)/", $params[$key], $url, 1);
            }
        }

        return $url;
    }

}
<?php

/**
 * Routing class to match request URL's against given routes and map them to a controller action.
 *
 * @author Danny
 */
final class Router {
    
    /**
     * Array to store named routes in, used for reverse routing.
     * @var array 
     */
    private $named_routes = array();
    
    /**
     * Boolean whether a route has been matched.
     * @var boolean
     */
    private $route_found = false;
    
    /**
     * The matched route. Contains an array with controller, action and optional parameter values.
     * @var array 
     */
    private $route = array();
    
    /**
     * The base REQUEST_URI. Gets prepended to all route url's.
     * 
     * @var string
     */
    private $base_url = '';
    
    /**
     * Creates an instance of the Router class
     * @param string $base_url Base url to prepend to all route url's (optional)
     */
    public function __construct($base_url = '') {
        $this->base_url = $base_url;
    }
    
    /**
     * Set the base url - gets prepended to all route url's.
     * @param string $base_url 
     */
    public function setBaseUrl($base_url) {
       $this->base_url = $base_url;
    }
    
    /**
     * Has a route been matched?
     * @return boolean True if a route has been found, false if not. 
     */
    public function hasRoute() {
        return $this->route_found;
    }
    
    /**
     * Get array with data of the matched route.
     * @return array Array containing the controller, action and parameters of matched route. 
     */
    public function getRoute() {
        return $this->route;
    }

    
    /**
     * Match a route to the current REQUEST_URI. Returns true on succes (route matches), false on failure.
     * 
     * @param string $route_url The URL of the route to match, must start with a leading slash. Dynamic URL value's must start with a colon. 
     * @param string $target The controller and action to map this route_url to, seperated by a hash (#). The action value defaults to 'index'. (optional)
     * @param array $args Accepts two keys, 'via' and 'as'. 'via' accepts a comma seperated list of HTTP Methods for this route. 'as' accepts a string and will be used as the name of this route.
     * @return boolean True if route matches URL, false if not.
     */
    public function match($route_url, $target = '', array $args = array()) {
              
        // check if this is a named route, if so, store it.
        if (isset($args['as'])) {
            $this->named_routes[$args['as']] = $route_url;
        }
        
        // check if a route has already been found
        // if so, function doesn't have to run
        if($this->route_found) return;

        // check for matching method
        if (isset($args['via'])) {

            // all methods uppercase
            $args['via'] = strtoupper($args['via']);

            // explode by comma
            $request_methods = explode(',', $args['via']);

            // hack to simulate DELETE and PUT requests
            if (isset($_POST['_method']) && ($_method = strtoupper($_POST['_method'])) && in_array($_method, array('PUT', 'DELETE'))) {
                $server_request_method = $_method;
            } else {
                $server_request_method = $_SERVER['REQUEST_METHOD'];
            }

            // check if current request has the right method for this route. if not, return false.
            if (!in_array($_SERVER['REQUEST_METHOD'], $request_methods))
                return false;
        }

        // check for matching URL
        $request_url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $request_url = strtolower(rtrim($request_url, '/'));

        // setup route regex for route url
        $route_regex = preg_replace("/:(\w+)/", "(\w+)", $this->base_url.$route_url);

        // check if request url matches route regex. if not, return false.
        if (!preg_match("@^{$route_regex}*$@", $request_url, $matches))
            return false;
        
        // setup parameters
        $params = array();

        // fill params array
        if (preg_match_all("/:(\w+)/", $route_url, $param_keys)) {

            // grab array with matches
            $param_keys = $param_keys[1];

            // loop trough parameter names, store matching value in $params array
            foreach ($param_keys as $key => $name) {
                if (isset($matches[$key + 1]))
                    $params[$name] = $matches[$key + 1];
            }
        }

        if ($target) {
            // target explicitly given
            $target = explode('#', $target);

            $controller = $target[0];
            $action = (isset($target[1])) ? $target[1] : 'index';
        } else {
            // target not explicitly given
            // extract from url
            $target = explode('/', ltrim(str_replace($this->base_url,'',$request_url),'/'));
            var_dump($target);

            $controller = $target[0];
            $action = (isset($target[1])) ? $target[1] : 'index';
        }

        $this->route_found = true;
        $this->route = array('controller' => $controller, 'action' => $action, 'params' => $params);
        return true;
    }
    
    /**
     * Matches REST URL's for a given controller
     * 
     * @param string $controller The controller name
     * @param array $args Accepts 'only' and 'except' keys. If given, function only matches given methods (or excludes them, depending on what key you gave).
     */
    public function resources($controller, array $args = array()) {
        $routes = array(
            'index' => array($controller, "$controller#index", array('via' => 'GET')),
            'new' => array("$controller/new", "$controller#new", array('via' => 'GET')),
            'show' => array("$controller/:id", "$controller#show", array('via' => 'GET')),
            'edit' => array("$controller/:id/edit", "$controller#edit", array('via' => 'GET')),
            'update' => array("$controller/:id", "$controller#update", array('via' => 'PUT')),
            'destroy' => array("$controller/:id", "$controller#destroy", array('via' => 'DELETE')),
            'create' => array("$controller", "$controller#create", array('via' => 'POST'))
        );

        
        if (isset($args['only'])) {
            // only route to specified methods
            $only = explode(',', $args['only']);

            foreach ($only as $o) {
                if (isset($routes[$o]))
                    $this->match($routes[$o][0], $routes[$o][1], $routes[$o][2]);
            }
            
            // abandon
            return;
        } elseif (isset($args['except'])) {

            $except = explode(',', $args['except']);

            // unset specified routes
            foreach ($except as $e) {
                unset($routes[$e]);
            }
        }
        
        // loop all routes
        foreach ($routes as $r) {
            $this->match($r[0], $r[1], $r[2]);
        }
    }
    
    /**
     * Reverse route a named route
     * 
     * @param string $route_name The name of the route to reverse route.
     * @param array $params Optional array of parameters to use in URL
     * @return string The url to the route
     */
    public function reverse($route_name,array $params = array()) {
        // Check if route exists
        if(!isset($this->named_routes[$route_name])) return false;
        
        $route_url = $this->named_routes[$route_name];
        
        // replace route url with given parameters
        if($params && preg_match_all("/:(\w+)/", $route_url, $param_keys)) {

            // grab array with matches
            $param_keys = $param_keys[1];

            // loop trough parameter names, store matching value in $params array
            foreach ($param_keys as $i => $key) {
                if (isset($params[$key]))
                    $route_url = preg_replace("/:(\w+)/",$params[$key],$route_url,1);
            }
        }
        
        return $route_url;
          
    }


}
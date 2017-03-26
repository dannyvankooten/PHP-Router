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

use Fig\Http\Message\RequestMethodInterface;
use InvalidArgumentException;

class Route
{
    /**
     * URL of this Route
     * @var string
     */
    private $url;

    /**
     * Separator used between class and action
     * @const string
     */
    const ACTION_SEPARATOR = '::';

    /**
     * Accepted HTTP methods for this route.
     * @var string[]
     */
    private $methods = [
        RequestMethodInterface::METHOD_GET,
        RequestMethodInterface::METHOD_POST,
        RequestMethodInterface::METHOD_PUT,
        RequestMethodInterface::METHOD_DELETE,
    ];

    /**
     * Target controller class
     * @var string
     */
    private $controllerClassName;

    /**
    * Method called into the controller class
    * @var string
    */
    private $action;

    /**
     * Target for this route, can be anything.
     * @var mixed
     */
    private $target;

    /**
     * The name of this route, used for reversed routing
     * @var string
     */
    private $name;

    /**
     * Custom parameter filters for this route
     * @var array
     */
    private $filters = [];

    /**
     * Regex used to validate filtername
     * @var string
     */
    private $filtersRegex = ':([a-zA-Z_]+)';

    /**
     * Array containing parameters passed through request URL
     * @var array
     */
    private $parameters = [];

    /**
     * Array containing parameters passed through config
     * @var array
     */
    private $customParams = [];

    /**
     * Set named parameters to target method
     * @example [ [0] => [ ["link_id"] => "12312" ] ]
     * @var bool
     */
    private $parametersByName;

    /**
     * @var array
     */
    private $config;

    /**
     * @param       $resource
     * @param array $config
     */
    public function __construct($resource, array $config)
    {
        $this->url          = $resource;
        $this->config       = $config;
        $this->methods      = isset($config['methods']) ? (array) $config['methods'] : [];
        $this->target       = isset($config['target']) ? $config['target'] : null;
        $this->name         = isset($config['name']) ? $config['name'] : null;
        $this->customParams = isset($config['parameters']) ? (array) $config['parameters'] : [];

        $action = explode(self::ACTION_SEPARATOR, $this->config['_controller']);
        $this->controller = $action[0];
        $this->action = !empty($action[1]) && trim($action[1]) !== '' ? $action[1] : null;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $url = (string)$url;

        // make sure that the URL is suffixed with a forward slash
        if (substr($url, -1) !== '/') {
            $url .= '/';
        }

        $this->url = $url;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function setTarget($target)
    {
        $this->target = $target;
    }

    public function getMethods()
    {
        return $this->methods;
    }

    public function setMethods(array $methods)
    {
        $this->methods = $methods;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = (string)$name;
    }

    public function setFilters(array $filters, $parametersByName = false)
    {
        $this->filters          = $filters;
        $this->parametersByName = $parametersByName;
        $this->validateFilters();
    }

    public function getRegex()
    {
        return preg_replace_callback('/(:\w+)/', [&$this, 'substituteFilter'], $this->url);
    }

    private function substituteFilter($matches)
    {
        if (isset($matches[1], $this->filters[$matches[1]])) {
            return $this->filters[$matches[1]];
        }

        return '([\w-%]+)';
    }

    /**
     * Validate filters to avoid wrong match while filters are not correctly defined
     *
     * @author Antoine Pous
     * @since 1.3.0
     * @param array $filters Filters to validate
     * @throws Exception Invalid filter throw exception
     * @return null
     */
    private function validateFilters()
    {
        foreach ($this->filters as $key => $reg) {
            if (!preg_match('~^' . $this->filtersRegex . '$~', $key)) {
                throw new InvalidArgumentException('Filter name `'.$key.'` does not match `^'.$this->filtersRegex.'$`');
            }
        }
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function dispatch()
    {
        $controllerClassName = $this->controller;

        // Merge parameters set from config and parameters given by filters
        // filter overload custom params
        $this->parameters = array_merge($this->customParams, $this->parameters);

        if ($this->parametersByName) {
            $this->parameters = [$this->parameters];
        }

        $instance = new $controllerClassName;
        call_user_func_array([$instance, $this->action], $this->parameters);
    }

    /**
     * Set action
     *
     * @author Antoine Pous
     * @since 1.3.0
     * @param string $action Action to call
     * @return null
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * Get Action
     *
     * @author Antoine Pous
     * @since 1.3.0
     * @param string $action Action to call
     * @return string|null The action
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set controller
     *
     * @author Antoine Pous
     * @since 1.3.0
     * @param string $controller Controller to call
     * @return null
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * Get controller
     *
     * @author Antoine Pous
     * @since 1.3.0
     * @return string|null The controller
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Get filter regular expression
     *
     * @author Antoine Pous
     * @since 1.3.0
     * @return string The regex used to match filters names
     */
    public function getFiltersRegex()
    {
        return $this->filtersRegex;
    }

    /**
     * Set filter regular expression
     *
     * @author Antoine Pous
     * @since 1.3.0
     * @param string The new regex used to match filters names
     * @return null
     */
    public function setFiltersRegex($regex)
    {
        $this->filtersRegex = $regex;
    }
}

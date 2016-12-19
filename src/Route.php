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
use Exception;
use Interop\Container\ContainerInterface;
use SebastianBergmann\GlobalState\RuntimeException;

class Route
{
    /**
     * URL of this Route
     * @var string
     */
    private $url;

    /**
     * Accepted HTTP methods for this route.
     * @var string[]
     */
    private $methods = array(
        RequestMethodInterface::METHOD_GET,
        RequestMethodInterface::METHOD_POST,
        RequestMethodInterface::METHOD_PUT,
        RequestMethodInterface::METHOD_DELETE,
    );

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
    private $filters = array();

    /**
     * Array containing parameters passed through request URL
     * @var array
     */
    private $parameters = array();

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
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $action;

    /**
     * @var string
     */
    private $controller;

    /**
     * @param       $resource
     * @param array $config
     */
    public function __construct($resource, array $config)
    {
        $this->url    = $resource;
        $this->config = $config;

        list($controller, $action) = explode('::', $this->config['_controller']);

        $this->controller = $controller;
        $this->action     = $action;
        $this->methods    = isset($config['methods']) ? (array) $config['methods'] : array();
        $this->target     = isset($config['target']) ? $config['target'] : null;
        $this->name       = isset($config['name']) ? $config['name'] : null;
        $this->parameters = isset($config['parameters']) ? $config['parameters'] : array();
    }

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
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
        return preg_replace_callback('/(:\w+)/', array(&$this, 'substituteFilter'), $this->url);
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
     * @throw Exception Invalid filter throw exception
     * @return null
     */
    private function validateFilters()
    {
        foreach($this->filters as $key => $reg) {
            if(!preg_match('~^:([[a-z]])$~i', $key)) {
                throw new Exception('Invalid filter name `'.$key.'` it should contains only letters and start with `:`');
            }
        }
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters)
    {
        $this->parameters = array_merge($this->parameters, $parameters);
    }

    public function dispatch()
    {
        if (null === $this->getAction()) {
            throw new RuntimeException(
                sprintf('Could not find an action to controller "%s"', $this->controller)
            );
        }

        if ($this->parametersByName) {
            $this->parameters = array($this->parameters);
        }

        $controller = $this->controller;

        if ($this->container && $this->container->has($controller)) {
            $instance = $this->container->get($controller);
        } else {
            $instance = new $controller;
        }

        if (method_exists($this, $this->getAction())){
            call_user_func_array(
                array($instance, $this->getAction()),
                $this->parameters
            );

            return;
        }

        new $controller($this->parameters);
    }

    public function getAction()
    {
        return '' !== trim($this->action) ? $this->action : null;
    }
}

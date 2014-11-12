<?php
namespace PHPRouter;

use PHPRouter\DI\ContainerInterface;
use PHPRouter\DI\InjectableInterface;
use PHPRouter\DI\Exceptions\InjectionException;

class Route
{
    /**
    * URL of this Route
    * @var string
    */
    private $_url;

    /**
    * Accepted HTTP methods for this route
    * @var array
    */
    private $_methods = array('GET', 'POST', 'PUT', 'DELETE');

    /**
    * Target for this route, can be anything.
    * @var mixed
    */
    private $_target;

    /**
    * The name of this route, used for reversed routing
    * @var string
    */
    private $_name;

    /**
    * Custom parameter filters for this route
    * @var array
    */
    private $_filters = array();

    /**
    * Array containing parameters passed through request URL
    * @var array
    */
    private $_parameters = array();

    /**
     * Service container
     * @var object
     */
    private $_container;

    /**
     * @param $resource
     * @param array $config
     */
    public function __construct($resource, array $config, ContainerInterface $container = null)
    {
        $this->_url = $resource;
        $this->_config = $config;
        $this->_container= $container;

        $this->_methods = isset($config['methods']) ? $config['methods'] : array();
        $this->_target = isset($config['target']) ? $config['target'] : null;
    }

    public function getUrl()
    {
        return $this->_url;
    }

    public function setUrl($url)
    {
        $url = (string) $url;

        // make sure that the URL is suffixed with a forward slash
        if(substr($url,-1) !== '/') $url .= '/';

        $this->_url = $url;
    }

    public function getTarget()
    {
        return $this->_target;
    }

    public function setTarget($target)
    {
        $this->_target = $target;
    }

    public function getMethods()
    {
        return $this->_methods;
    }

    public function setMethods(array $methods)
    {
        $this->_methods = $methods;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function setName($name)
    {
        $this->_name = (string) $name;
    }

    public function setFilters(array $filters)
    {
        $this->_filters = $filters;
    }

    public function getRegex()
    {
       return preg_replace_callback("/:(\w+)/", array(&$this, 'substituteFilter'), $this->_url);
    }

    private function substituteFilter($matches)
    {
        if (isset($matches[1]) && isset($this->_filters[$matches[1]])) {
            return $this->_filters[$matches[1]];
        }

        return "([\w-%]+)";
    }

    public function getParameters()
    {
        return $this->_parameters;
    }

    /**
     * @return object
     */
    public function getContainer()
    {
        return $this->_container;
    }



    public function setParameters(array $parameters)
    {
        $this->_parameters = $parameters;
    }

    public function dispatch()
    {
        $action = explode('::', $this->_config['_controller']);
        $instance = new $action[0];

        if($this->getContainer() && $instance instanceof InjectableInterface){
            $instance->setServiceContainer($this->getContainer());
        }else{
            throw new InjectionException('To inject container you need to implement InjectableInterface');
        }

        call_user_func_array(array($instance, $action[1]), $this->_parameters);
    }


}

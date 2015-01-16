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
     * @param $resource
     * @param array $config
     */
    public function __construct($resource, array $config)
    {
        $this->_url = $resource;
        $this->_config = $config;
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

    public function setParameters(array $parameters)
    {
        $this->_parameters = $parameters;
    }

    public function dispatch()
    {
        $action = explode('::', $this->_config['_controller']);
        $instance = new $action[0];
        $instance->$action[1]();
    }
}

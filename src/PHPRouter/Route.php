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
    private $url;

    /**
     * Accepted HTTP methods for this route.
     *
     * @var string[]
     */
    private $methods = ['GET', 'POST', 'PUT', 'DELETE'];

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
     * Array containing parameters passed through request URL
     * @var array
     */
    private $parameters = [];

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
        $this->url     = $resource;
        $this->config  = $config;
        $this->methods = $config['methods'] ?: [];
        $this->target  = $config['target']  ?: null;
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

    public function setFilters(array $filters)
    {
        $this->filters = $filters;
    }

    public function getRegex()
    {
        return preg_replace_callback("/:(\w+)/", [&$this, 'substituteFilter'], $this->url);
    }

    private function substituteFilter($matches)
    {
        if (isset($matches[1]) && isset($this->filters[$matches[1]])) {
            return $this->filters[$matches[1]];
        }

        return "([\w-%]+)";
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
        $action = explode('::', $this->config['_controller']);
        $instance = new $action[0];
        $instance->$action[1]();
    }
}

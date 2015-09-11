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
namespace PHPRouterTest\Test;

require __DIR__ . '/../../Fixtures/SomeController.php';

use PHPRouter\Config;
use PHPRouter\Route;
use PHPRouter\Router;
use PHPRouter\RouteCollection;
use PHPUnit_Framework_TestCase;

class RouterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider matcherProvider
     *
     * @param Router $router
     * @param string $path
     * @param string $expected
     */
    public function testMatch($router, $path, $expected)
    {
        $this->assertEquals($expected, (bool) $router->match($path));
    }

    public function testBasePathConfigIsSettedProperly()
    {
        $router =  new Router(new RouteCollection);
        $router->setBasePath('/webroot/');

        $this->assertAttributeEquals('/webroot', 'basePath', $router);
    }

    public function testMatchRouterUsingBasePath()
    {
        $collection = new RouteCollection();
        $collection->attach(new Route('/users/', array(
            '_controller' => 'PHPRouter\Test\SomeController::users_create',
            'methods' => 'GET'
        )));

        $router =  new Router($collection);
        $router->setBasePath('/localhost/webroot');

        $_SERVER                   = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI']    = '/localhost/webroot/users/';
        $_SERVER['SCRIPT_NAME']    = 'index.php';

        $this->assertTrue((bool) $router->matchCurrentRequest());
    }

    /**
     * @covers Router::match
     * @covers Route::getParameters
     */
    public function testGetParamsInsideControllerMethod()
    {
        $collection = new RouteCollection();
        $route = new Route(
            '/page/:page_id',
            array(
                '_controller' => 'PHPRouter\Test\SomeController::page',
                'methods' => 'GET'
            )
        );
        $route->setFilters(array('page_id' => '([a-zA-Z]+)'), true);
        $collection->attachRoute($route);

        $router = new Router($collection);
        $this->assertEquals(
            array(array('page_id' => 'MySuperPage')),
            $router->match('/page/MySuperPage')->getParameters()
        );
    }

    /**
     * @covers Router::match
     * @covers Route::getParameters
     */
    public function testParamsWithDynamicFilterMatch()
    {
        $collection = new RouteCollection();
        $route = new Route(
            '/js/:filename.js',
            array(
                '_controller' => 'PHPRouter\Test\SomeController::dynamicFilterUrlMatch',
                'methods' => 'GET',
            )
        );
        $route->setFilters(array(':filename' => '([[:alnum:]\.]+)'), true);
        $collection->attachRoute($route);

        $router = new Router($collection);
        $this->assertEquals(
            array(array('filename' => 'someJsFile')),
            $router->match('/js/someJsFile.js')->getParameters()
        );

        $this->assertEquals(
            array(array('filename' => 'someJsFile.min')),
            $router->match('/js/someJsFile.min.js')->getParameters()
        );

        $this->assertEquals(
            array(array('filename' => 'someJsFile.min.js')),
            $router->match('/js/someJsFile.min.js.js')->getParameters()
        );
    }

    public function testYaml(){
        $config = Config::loadFromFile(__DIR__ . '/../../Fixtures/router.yaml');
        $router = Router::parseConfig($config);
        $this->assertEquals(
            array(array('param' => '1234')),
            $router->match('/blog/article/1234')->getParameters()
        );
    }

    /**
     * @return Router
     */
    private function getRouter()
    {
        $collection = new RouteCollection();
        $collection->attachRoute(new Route('/users/', array(
            '_controller' => 'PHPRouter\Test\SomeController::users_create',
            'methods' => 'GET'
        )));
        $collection->attachRoute(new Route('/user/:id', array(
            '_controller' => 'PHPRouter\Test\SomeController::user',
            'methods' => 'GET'
        )));
        $collection->attachRoute(new Route('/', array(
            '_controller' => 'PHPRouter\Test\SomeController::indexAction',
            'methods' => 'GET'
        )));

        return new Router($collection);
    }

    /**
     * @return mixed[][]
     */
    public function matcherProvider1()
    {
        $router = $this->getRouter();
        return array(
            array($router, '', true),
            array($router, '/', true),
            array($router, '/aaa', false),
            array($router, '/users', true),
            array($router, '/user/1', true),
            array($router, '/user/%E3%81%82', true),
        );
    }

    /**
     * @return mixed[][]
     */
    public function matcherProvider2()
    {
        $router = $this->getRouter();
        $router->setBasePath('/api');
        return array(
            array($router, '', false),
            array($router, '/', false),
            array($router, '/aaa', false),
            array($router, '/users', false),
            array($router, '/user/1', false),
            array($router, '/user/%E3%81%82', false),

            array($router, '/api', true),
            array($router, '/api/aaa', false),
            array($router, '/api/users', true),
            array($router, '/api/user/1', true),
            array($router, '/api/user/%E3%81%82', true),
        );
    }

    /**
     * @return string[]
     */
    public function matcherProvider()
    {
        return array_merge($this->matcherProvider1(), $this->matcherProvider2());
    }
}

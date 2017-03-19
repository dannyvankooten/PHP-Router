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

use PHPRouter\Route;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
    private $routeWithParameters;
    private $routeWithoutAction;

    protected function setUp()
    {
        $this->routeWithParameters = new Route(
            '/page/:page_id',
            array(
                '_controller' => 'PHPRouter\Test\SomeController::page',
                'methods' => array(
                    'GET'
                ),
                'target' => 'thisIsAString',
                'name' => 'page'
            )
        );

        $this->routeWithoutAction = new Route(
            '/page/:page_id',
            array(
                '_controller' => 'PHPRouter\Test\SomeController',
                'methods' => array(
                    'GET'
                ),
                'target' => 'thisIsAString',
                'name' => 'page'
            )
        );
    }

    public function testGetUrl()
    {
        self::assertEquals('/page/:page_id', $this->routeWithParameters->getUrl());
    }

    public function testSetUrl()
    {
        $this->routeWithParameters->setUrl('/pages/:page_name/');
        self::assertEquals('/pages/:page_name/', $this->routeWithParameters->getUrl());

        $this->routeWithParameters->setUrl('/pages/:page_name');
        self::assertEquals('/pages/:page_name/', $this->routeWithParameters->getUrl());
    }

    public function testGetMethods()
    {
        self::assertEquals(array('GET'), $this->routeWithParameters->getMethods());
    }

    public function testSetMethods()
    {
        $this->routeWithParameters->setMethods(array('POST'));
        self::assertEquals(array('POST'), $this->routeWithParameters->getMethods());

        $this->routeWithParameters->setMethods(array('GET', 'POST', 'PUT', 'DELETE'));
        self::assertEquals(array('GET', 'POST', 'PUT', 'DELETE'), $this->routeWithParameters->getMethods());
    }

    public function testGetTarget()
    {
        self::assertEquals('thisIsAString', $this->routeWithParameters->getTarget());
    }

    public function testSetTarget()
    {
        $this->routeWithParameters->setTarget('ThisIsAnotherString');
        self::assertEquals('ThisIsAnotherString', $this->routeWithParameters->getTarget());
    }

    public function testGetName()
    {
        self::assertEquals('page', $this->routeWithParameters->getName());
    }

    public function testSetName()
    {
        $this->routeWithParameters->setName('pageroute');
        self::assertEquals('pageroute', $this->routeWithParameters->getName());
    }

    public function testGetAction()
    {
        self::assertEquals('page', $this->routeWithParameters->getAction());
        self::assertEquals('__construct', $this->routeWithoutAction->getAction());
    }

    public function testGetController()
    {
        self::assertEquals('PHPRouter\Test\SomeController', $this->routeWithParameters->getController());
        self::assertEquals('PHPRouter\Test\SomeController', $this->routeWithoutAction->getController());
    }
}

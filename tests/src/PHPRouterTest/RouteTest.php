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
namespace PHPRouterTest;

use PHPRouter\Route;
use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{
    private $routeWithParameters;
    private $routeWithoutAction;

    protected function setUp()
    {
        $this->routeWithParameters = new Route(
            '/page/:page_id',
            [
                '_controller' => 'PHPRouterFixtures\SomeController::page',
                'methods' => [
                    'GET'
                ],
                'target' => 'thisIsAString',
                'name' => 'page'
            ]
        );

        $this->routeWithoutAction = new Route(
            '/page/:page_id',
            [
                '_controller' => 'PHPRouterFixtures\SomeController',
                'methods' => [
                    'GET'
                ],
                'target' => 'thisIsAString',
                'name' => 'page'
            ]
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
        self::assertEquals(['GET'], $this->routeWithParameters->getMethods());
    }

    public function testSetMethods()
    {
        $this->routeWithParameters->setMethods(['POST']);
        self::assertEquals(['POST'], $this->routeWithParameters->getMethods());

        $this->routeWithParameters->setMethods(['GET', 'POST', 'PUT', 'DELETE']);
        self::assertEquals(['GET', 'POST', 'PUT', 'DELETE'], $this->routeWithParameters->getMethods());
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
        self::assertEquals(null, $this->routeWithoutAction->getAction());
    }

    public function testGetController()
    {
        self::assertEquals('PHPRouterFixtures\SomeController', $this->routeWithParameters->getController());
        self::assertEquals('PHPRouterFixtures\SomeController', $this->routeWithoutAction->getController());
    }

    public function testSetController()
    {
        $this->routeWithParameters->setController('PHPRouterFixtures\anotherController');
        self::assertEquals('PHPRouterFixtures\anotherController', $this->routeWithParameters->getController());
    }

    public function testSetAction()
    {
        $this->routeWithParameters->setAction('anotherAction');
        self::assertEquals('anotherAction', $this->routeWithParameters->getAction());
    }

    public function testGetFiltersRegex()
    {
        self::assertEquals(':([a-zA-Z_]+)', $this->routeWithParameters->getFiltersRegex());
    }

    public function testSetFiltersRegex()
    {
        $this->routeWithParameters->setFiltersRegex(':([a-zA-Z_]+):');
        self::assertEquals(':([a-zA-Z_]+):', $this->routeWithParameters->getFiltersRegex());
    }
}

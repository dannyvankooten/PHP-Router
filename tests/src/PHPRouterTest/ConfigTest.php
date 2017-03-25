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

use PHPRouter\Config;
use PHPUnit\Framework\TestCase;

/**
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @package PHPRouter\Test
 */
final class ConfigTest extends TestCase
{

    protected $expectedData = [
        'base_path' => '/blog',
        'routes' => [
            'index' => [
                'route' => '/index',
                '_controller' => 'someController',
                'methods' => [
                    'GET'
                ],
            ],
            'article' => [
                'route' => '/article/:id/:title_slug',
                '_controller' => 'PHPRouterFixtures\someControllerdynamic::FilterUrlMatch',
                'methods' => [
                    'GET'
                ],
                'filters' => [
                    ':id' => '([\d]+)',
                    ':title_slug' => '([[:alnum:]_-]+)'
                ]
            ],
            'contact' => [
                'route' => '/contact',
                '_controller' => 'PHPRouterFixtures\someController::page',
                'methods' => [
                    'GET',
                    'POST'
                ]
            ]
        ]
    ];
    /**
     * @throws \InvalidArgumentException
     */
    public function testConfigThrowsErrorWithWrongParameter()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('The file fileNotExisting does not exists !');
        Config::loadFromJSONFile('fileNotExisting');
    }

    public function testConfigFileCanReadAndReturnDataOfAJsonFile()
    {
        $result = Config::loadFromJSONFile(__DIR__ . '/../../Fixtures/router.json');
        self::assertSame($this->expectedData, $result);
    }

    public function testConfigFileCanReadAndReturnDataOfAYamlFile()
    {
        $result = Config::loadFromYAMLFile(__DIR__ . '/../../Fixtures/router.yaml');
        self::assertSame($this->expectedData, $result);
    }
}

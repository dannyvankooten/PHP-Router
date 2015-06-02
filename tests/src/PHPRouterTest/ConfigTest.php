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

use PHPRouter\Config;
use PHPUnit_Framework_TestCase;

/**
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @package PHPRouter\Test
 */
class ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @throws \InvalidArgumentException
     */
    public function testConfigThrowsErrorWithWrongParameter()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'The file fileNotExisting not exists!'
        );
        Config::loadFromFile('fileNotExisting');
    }

    public function testConfigFileCanReadAndReturnDataOfAYamlFile()
    {
        $expected = array(
            'base_path' => '/blog',
            'routes'    => array(
                'index' => array(
                    '/index',
                    'Controller.method',
                    'GET'
                ),
                'contact' => array(
                    '/contact',
                    'someClass.contactAction',
                    'GET',
                ),
                'about' => array(
                    '/about',
                    'someClass.aboutAction',
                    'GET',
                )
            )
        );
        $result = Config::loadFromFile(__DIR__ . '/../../Fixtures/router.yaml');

        $this->assertSame($expected, $result);
    }
}

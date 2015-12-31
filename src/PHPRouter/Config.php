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

use InvalidArgumentException;
use Symfony\Component\Yaml\Yaml;

/**
 * Auxiliary Config class, to parse a Yaml file.
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @package PHPRouter
 */
final class Config
{
    /**
     * Avoid instantiation.
     */
    private function __construct()
    {
    }

    /**
     * @param string $yamlFile file location.
     * @throws InvalidArgumentException
     *
     * @return mixed[]
     */
    public static function loadFromFile($yamlFile)
    {
        if (! is_file($yamlFile)) {
            throw new InvalidArgumentException(sprintf('The file %s not exists!', $yamlFile));
        }

        return Yaml::parse(file_get_contents($yamlFile));
    }
}

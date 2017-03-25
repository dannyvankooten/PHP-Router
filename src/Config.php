<?php
declare(strict_types=1);
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
use RuntimeException;
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
     *
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * Load config from YAML file
     * @since 1.3.0
     * @param string $yamlFile File location
     * @throws InvalidArgumentException
     *
     * @return array Router configuration
     */
    public static function loadFromYAMLFile(string $yamlFile) : array
    {
        if (!$yamlConfig = Yaml::parse(self::getConfigFileContents($yamlFile))) {
            throw new InvalidArgumentException(sprintf('The content of the %s file is not a valid YAML !', $yamlFile));
        }

        return $yamlConfig;
    }

    /**
     * Load config from JSON file
     * @since 1.3.0
     * @param string $jsonFile File location
     * @throws InvalidArgumentException
     *
     * @return array Router configuration
     */
    public static function loadFromJSONFile(string $jsonFile) : array
    {
        $jsonConfig = json_decode(self::getConfigFileContents($jsonFile), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException(sprintf('The content of the %s file is not a valid JSON !', $jsonFile));
        }

        return $jsonConfig;
    }

    /**
     * Load config file and return it's contents
     * @since 1.3.0
     * @param string $file File location
     * @codeCoverageIgnore
     * @throws InvalidArgumentException | RuntimeException
     *
     * @return string File contents
     */
    private static function getConfigFileContents(string $file) : string
    {
        if (!is_file($file)) {
            throw new InvalidArgumentException(sprintf('The file %s does not exists !', $file));
        }

        if (!is_readable($file)) {
            throw new RuntimeException(sprintf('The file %s is not readable !', $file));
        }

        return file_get_contents($file);
    }
}

<?php
namespace PHPRouter;

use Symfony\Component\Yaml\Yaml;

class Config
{
    public static function loadFromFile($yamlFile)
    {
        try {
            $value = Yaml::parse(file_get_contents($yamlFile));
        } catch (\Exception $e) {
            echo 'Message %s'.$e->getMessage();
        }
        return $value;
    }
}

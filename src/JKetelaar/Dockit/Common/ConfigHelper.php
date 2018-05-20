<?php
/**
 * @author JKetelaar
 */

namespace JKetelaar\Dockit\Common;

use JsonMapper;
use ReflectionClass;
use ReflectionProperty;

/**
 * Class ConfigHelper
 * @package JKetelaar\Dockit\Common
 */
class ConfigHelper
{
    /**
     * @var JsonMapper
     */
    private static $jsonMapper;

    /**
     * @var Config
     */
    private static $config;

    /**
     * @return string
     */
    private static final function getConfigFile(): string
    {
        return getcwd() . '/private/dockit/config.json';
    }

    /**
     * @return JsonMapper
     */
    private static final function getJsonMapper(): JsonMapper
    {
        if (self::$jsonMapper === null) {
            self::$jsonMapper = new JsonMapper();
        }

        return self::$jsonMapper;
    }

    /**
     * @return Config
     */
    public static function getConfig(): Config
    {
        if (self::$config === null) {
            /** @var Config $config */
            $config = null;
            if (file_exists(self::getConfigFile())) {
                $config = self::getJsonMapper()->map(json_decode(file_get_contents(self::getConfigFile())), new Config());
            } else {
                $config = new Config();
            }

            self::setConfig($config);
        }

        return self::$config;
    }

    /**
     * @param Config $config
     */
    public static function setConfig(Config $config)
    {
        $configData = [];
        $reflectionClass = new ReflectionClass($config);
        $properties = $reflectionClass->getProperties(ReflectionProperty::IS_PROTECTED);
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $configData[$property->getName()] = $property->getValue($config);
        }

        file_put_contents(self::getConfigFile(), json_encode($configData));

        self::$config = $config;
    }
}
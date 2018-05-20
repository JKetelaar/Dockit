<?php
/**
 * @author JKetelaar
 */

namespace JKetelaar\Dockit\Common;

use JKetelaar\Dockit\Configuration\ProjectConfiguration;
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
     * @var ProjectConfiguration
     */
    private static $config;

    /**
     * @return ProjectConfiguration
     * @throws \JsonMapper_Exception
     * @throws \ReflectionException
     */
    public static function getConfig(): ProjectConfiguration
    {
        if (self::$config === null) {
            /** @var ProjectConfiguration $config */
            $config = null;
            if (file_exists(self::getConfigFile())) {
                $config = self::getJsonMapper()->map(
                    json_decode(file_get_contents(self::getConfigFile())),
                    new ProjectConfiguration()
                );
            } else {
                $config = new ProjectConfiguration();
            }

            self::setConfig($config);
        }

        return self::$config;
    }

    /**
     * @param ProjectConfiguration $config
     * @throws \ReflectionException
     */
    public static function setConfig(ProjectConfiguration $config)
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

    /**
     * @return string
     */
    public static final function getConfigFile(): string
    {
        return getcwd().'/private/dockit/config.json';
    }

    /**
     * @return JsonMapper
     */
    public static final function getJsonMapper(): JsonMapper
    {
        if (self::$jsonMapper === null) {
            self::$jsonMapper = new JsonMapper();
        }

        return self::$jsonMapper;
    }

    /**
     * @return bool
     */
    public static function hasConfig()
    {
        return file_exists(self::getConfigFile());
    }

    /**
     * @param string $file
     * @return ProjectConfiguration|null|object
     */
    public static function getConfigFromFile(string $file)
    {
        if (file_exists($file)) {
            try {
                return self::getJsonMapper()->map(
                    json_decode(file_get_contents($file)),
                    new ProjectConfiguration()
                );
            } catch (\JsonMapper_Exception $e) {
            }
        }

        return null;
    }
}
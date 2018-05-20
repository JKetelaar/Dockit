<?php
/**
 * @author JKetelaar
 */

namespace JKetelaar\Dockit\Configuration;

use JKetelaar\Dockit\Common\ConfigHelper;
use ReflectionClass;
use ReflectionProperty;

/**
 * Class GlobalConfiguration
 * @package JKetelaar\Dockit\Configuration
 */
class GlobalConfiguration
{
    /**
     * Paths to all projects
     *
     * @var string[]
     */
    protected $projects = [];

    /**
     * @var bool
     */
    protected $installRedis = true;

    /**
     * @return GlobalConfiguration|object
     */
    public static function get()
    {
        if (file_exists(self::getFile())) {
            try {
                return ConfigHelper::getJsonMapper()->map(
                    json_decode(file_get_contents(self::getFile())),
                    new GlobalConfiguration()
                );
            } catch (\JsonMapper_Exception $e) {
            }
        }

        return new GlobalConfiguration();
    }

    /**
     * @return string
     */
    private static function getFile(): string
    {
        return $_SERVER['HOME'].'/.dockit/config.json';
    }

    public static function addProject(string $projectDirectory){
        $config = self::get();

        $projects = $config->getProjects();
        $projects[] = $projectDirectory;
        $cleanedProjects = [];

        foreach ($projects as $project){
            if(file_exists($project) && !in_array($project, $cleanedProjects)){
                $cleanedProjects[] = $project;
            }
        }

        $config->setProjects($cleanedProjects);

        self::set($config);
    }

    /**
     * @param GlobalConfiguration $configuration
     * @throws \ReflectionException
     */
    public static function set(GlobalConfiguration $configuration)
    {
        $configData = [];
        $reflectionClass = new ReflectionClass($configuration);
        $properties = $reflectionClass->getProperties(ReflectionProperty::IS_PROTECTED);
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $configData[$property->getName()] = $property->getValue($configuration);
        }

        file_put_contents(self::getFile(), json_encode($configData));
    }

    /**
     * @return ProjectConfiguration[]
     */
    public function getProjectConfigurations(): array
    {
        $projects = [];
        foreach ($this->projects as $projectFile) {
            $project = ConfigHelper::getConfigFromFile($projectFile . '/private/dockit/config.json');
            if ($project !== null) {
                $projects[] = $project;
            }
        }

        return $projects;
    }

    /**
     * @return string[]
     */
    public function getProjects(): array
    {
        return $this->projects;
    }

    /**
     * @param string[] $projects
     *
     * @return GlobalConfiguration
     */
    public function setProjects(array $projects): GlobalConfiguration
    {
        $this->projects = $projects;

        return $this;
    }

    /**
     * @return bool
     */
    public function isInstallRedis(): bool
    {
        return $this->installRedis;
    }

    /**
     * @param bool $installRedis
     *
     * @return GlobalConfiguration
     */
    public function setInstallRedis(bool $installRedis): GlobalConfiguration
    {
        $this->installRedis = $installRedis;

        return $this;
    }
}
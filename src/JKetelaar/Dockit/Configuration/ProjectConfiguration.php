<?php
/**
 * @author JKetelaar
 */

namespace JKetelaar\Dockit\Configuration;

/**
 * Class ProjectConfiguration
 * @package JKetelaar\Dockit\Common
 */
class ProjectConfiguration
{
    /**
     * @var string
     */
    protected $projectName = 'default';

    /**
     * @var string
     */
    protected $cms = 'default';

    /**
     * @var string
     */
    protected $php = '7.1';

    /**
     * @return string
     */
    public function getProjectName(): string
    {
        return $this->projectName;
    }

    /**
     * @param string $projectName
     * @return ProjectConfiguration
     */
    public function setProjectName(string $projectName): ProjectConfiguration
    {
        $this->projectName = $projectName;

        return $this;
    }

    /**
     * @return string
     */
    public function getCms(): string
    {
        return $this->cms;
    }

    /**
     * @param string $cms
     * @return ProjectConfiguration
     */
    public function setCms(string $cms): ProjectConfiguration
    {
        $this->cms = $cms;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhp(): string
    {
        return $this->php;
    }

    /**
     * @param string $php
     * @return ProjectConfiguration
     */
    public function setPhp(string $php): ProjectConfiguration
    {
        $this->php = $php;

        return $this;
    }
}
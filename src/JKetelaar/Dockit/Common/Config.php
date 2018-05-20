<?php
/**
 * @author JKetelaar
 */

namespace JKetelaar\Dockit\Common;

/**
 * Class Config
 * @package JKetelaar\Dockit\Common
 */
class Config
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
     * @return Config
     */
    public function setProjectName(string $projectName): Config
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
     * @return Config
     */
    public function setCms(string $cms): Config
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
     * @return Config
     */
    public function setPhp(string $php): Config
    {
        $this->php = $php;

        return $this;
    }
}
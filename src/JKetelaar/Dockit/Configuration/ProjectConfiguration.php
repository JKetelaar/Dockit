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
     * @var string
     */
    protected $webService = 'nginx';

    /**
     * @var string
     */
    protected $domain = 'default.dockit.site';

    /**
     * @var array
     */
    protected $modules = [];

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

    /**
     * @return string
     */
    public function getWebService(): string
    {
        return $this->webService;
    }

    /**
     * @param string $webService
     *
     * @return ProjectConfiguration
     */
    public function setWebService(string $webService): ProjectConfiguration
    {
        $this->webService = $webService;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param mixed $domain
     *
     * @return ProjectConfiguration
     */
    public function setDomain($domain): ProjectConfiguration
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDomainWithTLD()
    {
        return $this->domain.'.dockit.site';
    }

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
     * @return array
     */
    public function getModules(): array
    {
        return $this->modules;
    }

    /**
     * @param array $modules
     *
     * @return ProjectConfiguration
     */
    public function setModules(array $modules): ProjectConfiguration
    {
        $this->modules = $modules;

        return $this;
    }

    /**
     * @return string
     */
    public function getDockerPhpVersion()
    {
        switch ($this->php) {
            case '5.6':
                return 'php56';
            case '7.0':
                return 'php70';
            case '7.1':
                return 'php71';
            case '7.2':
            default:
                return 'php72';
        }
    }

    /**
     * @return string
     */
    public function getDockerPhpPath()
    {
        switch ($this->php) {
            case '5.6':
                return '/etc/php5/';
            case '7.0':
            case '7.1':
            case '7.2':
            default:
                return '/etc/php/'.$this->php.'/';
        }
    }
}
<?php
/**
 * @author JKetelaar
 */

namespace JKetelaar\Dockit\Docker\Instance;

use Docker\Docker;

/**
 * Class DockerInstance
 * @package JKetelaar\Dockit\Docker
 */
class DockerInstance
{
    /**
     * @var Docker
     */
    private $docker;

    /**
     * DockerInstance constructor.
     */
    public function __construct()
    {
        $this->docker = Docker::create();
    }

    /**
     * @return Docker
     */
    public function getDocker(): Docker
    {
        return $this->docker;
    }
}
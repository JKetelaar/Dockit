<?php
/**
 * @author JKetelaar
 */

namespace JKetelaar\Dockit\Docker\Instance;

/**
 * Interface DockerInstanceInterface
 * @package JKetelaar\Dockit\Docker\Instance
 */
interface DockerInstanceInterface
{
    /**
     * @param DockerInstance $instance
     * @return void
     */
    public function setDockerInstance(DockerInstance $instance);
}
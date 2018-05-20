<?php
/**
 * @author JKetelaar
 */

namespace JKetelaar\Dockit\HAProxy;

use JKetelaar\Dockit\Common\CommandLine;
use JKetelaar\Dockit\Docker\Config;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ReverseProxy
 * @package JKetelaar\Dockit\HAProxy
 */
class ReverseProxy
{
    /**
     * @var Config
     */
    private $config;

    /**
     * ReverseProxy constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function createHAProxyConfig()
    {
        $this->config->createTemplateFile('dockit/haproxy.conf.twig',
            $_SERVER['HOME'] . '/.dockit/dockit/haproxy.conf', [
                'projects' => [
                    [
                        'name' => 'interiorworks',
                        'domain' => 'project-1.dockit.site',
                        'services' => [
                            'web',
                            'mysql',
                            'redis'
                        ]
                    ],
                    [
                        'name' => 'default',
                        'domain' => 'project-2.dockit.site',
                        'services' => ['web', 'mysql', 'redis']
                    ]
                ],
                'config' => $this->config
            ]);

        $this->config->createTemplateFile('dockit/docker-compose.yml.twig',
            $_SERVER['HOME'] . '/.dockit/dockit/docker-compose.yml', []);
    }

    /**
     * @param OutputInterface $output
     */
    public function restart(OutputInterface $output = null)
    {
        CommandLine::execute('docker-compose --log-level CRITICAL --file "' . $_SERVER['HOME'] . '/.dockit/dockit/docker-compose.yml" down',
            $output);

        CommandLine::execute('docker-compose --log-level CRITICAL --file "' . $_SERVER['HOME'] . '/.dockit/dockit/docker-compose.yml" up -d',
            $output);
    }
}
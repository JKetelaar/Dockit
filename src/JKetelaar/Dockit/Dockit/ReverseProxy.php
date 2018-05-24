<?php
/**
 * @author JKetelaar
 */

namespace JKetelaar\Dockit\Dockit;

use JKetelaar\Dockit\Common\CommandLine;
use JKetelaar\Dockit\Configuration\GlobalConfiguration;
use JKetelaar\Dockit\Docker\Config;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ReverseProxy
 * @package JKetelaar\Dockit\Dockit
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
        $config = GlobalConfiguration::get();
        $projects = $config->getProjectConfigurations();

        $this->config->createTemplateFile(
            'dockit/nginx.conf.twig',
            $_SERVER['HOME'].'/.dockit/dockit/nginx.conf',
            []
        );

        $this->config->createTemplateFile(
            'dockit/haproxy.conf.twig',
            $_SERVER['HOME'].'/.dockit/dockit/haproxy.conf',
            [
                'projects' => $projects,
                'config' => $this->config,
            ]
        );

        $this->config->createTemplateFile(
            'dockit/docker-compose.yml.twig',
            $_SERVER['HOME'].'/.dockit/dockit/docker-compose.yml',
            []
        );

        $config::set($config);
    }

    /**
     * @param OutputInterface $output
     */
    public function restart(OutputInterface $output = null)
    {
        $output->writeln('<info>Stopping dockit instances</info>');

        CommandLine::execute(
            'docker-compose --log-level CRITICAL --file "'.$_SERVER['HOME'].'/.dockit/dockit/docker-compose.yml" down',
            $output
        );

        $output->writeln('<info>Starting dockit instances</info>');
        CommandLine::execute(
            'docker-compose --log-level CRITICAL --file "'.$_SERVER['HOME'].'/.dockit/dockit/docker-compose.yml" up -d',
            $output
        );
    }
}
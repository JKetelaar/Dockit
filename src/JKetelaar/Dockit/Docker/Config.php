<?php
/**
 * @author JKetelaar
 */

namespace JKetelaar\Dockit\Docker;

use JKetelaar\Dockit\Common\ConfigHelper;
use JKetelaar\Dockit\Common\DockitCommand;
use JKetelaar\Dockit\HAProxy\ReverseProxy;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Twig_Environment;
use Twig_Loader_Filesystem;

/**
 * Class Config
 * @package JKetelaar\Dockit\Docker
 */
class Config implements DockitCommand
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var ReverseProxy
     */
    private $reverseProxy;

    /**
     * Config constructor.
     */
    public function __construct()
    {
        $loader = new Twig_Loader_Filesystem(__DIR__ . '/../../../../Resources/docker/');
        $this->twig = new Twig_Environment($loader);

        $this->createDirectory(getcwd() . '/private/dockit/');
        $this->createDirectory($_SERVER['HOME'] . '/.dockit/data/mysql');
        $this->createDirectory($_SERVER['HOME'] . '/.dockit/dockit/');

        $this->reverseProxy = new ReverseProxy($this);
    }

    private function createDirectory($directory)
    {
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    public function createDockerCompose()
    {
        $mysqlDir = $_SERVER['HOME'] . '/.dockit/data/mysql/' . strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $this->getProjectName()));

        $this->createTemplateFile('docker-compose.yml.twig', getcwd() . '/private/dockit/docker-compose.yml', [
            'ports' => ConfigHelper::getConfig()->getPorts(),

            'project' =>
                [
                    'name' => strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $this->getProjectName())),
                    'php' => $this->getPHPVersion(),
                    'directory' => getcwd(),
                    'mysql' => $mysqlDir,
                    'cms' => $this->getProjectCMS()
                ],
        ]);
    }

    /**
     * @param string $template
     * @param string $destination
     * @param array $options
     */
    public function createTemplateFile(string $template, string $destination, array $options = [])
    {
        try {
            $dockerCompose = $this->twig->render($template, $options);
            file_put_contents($destination, $dockerCompose);
        } catch (\Twig_Error_Loader $e) {
            var_dump($e->getMessage());
        } catch (\Twig_Error_Runtime $e) {
            var_dump($e->getMessage());
        } catch (\Twig_Error_Syntax $e) {
            var_dump($e->getMessage());
        }
    }

    /**
     * @return string
     */
    private function getPHPVersion(): string
    {
        return ConfigHelper::getConfig()->getPhp();
    }

    /**
     * @return string
     */
    private function getProjectName(): string
    {
        return ConfigHelper::getConfig()->getProjectName();
    }

    /**
     * @return string
     */
    private function getProjectCMS(): string
    {
        return ConfigHelper::getConfig()->getCms();
    }

    public function createNginx()
    {
        $this->createDirectory(getcwd() . '/private/dockit/nginx/');

        $this->createTemplateFile('docker/nginx/' . $this->getProjectCMS() . '/nginx.conf.twig',
            getcwd() . '/private/dockit/nginx/nginx.conf');
    }


    public function createPhpFPM()
    {
        $this->createDirectory(getcwd() . '/private/dockit/php-fpm/');

        $this->createTemplateFile('docker/php-fpm/php-ini-overrides.ini.twig',
            getcwd() . '/private/dockit/php-fpm/php-ini-overrides.ini');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param HelperSet $helperSet
     * @param array ...$parameters
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output, HelperSet $helperSet, array $parameters)
    {
        $output->writeln('<info>Creating docker-compose file</info>');
        $this->createDockerCompose();

        $output->writeln('<info>Creating Nginx files</info>');
        $this->createNginx();

        $output->writeln('<info>Creating PHP FPM files</info>');
        $this->createPhpFPM();

        $this->restartHAProxy($output);
    }

    /**
     * @return Twig_Environment
     */
    public function getTwig(): Twig_Environment
    {
        return $this->twig;
    }

    public function restartHAProxy(OutputInterface $output)
    {
        $this->reverseProxy->createHAProxyConfig();
        $this->reverseProxy->restart($output);
    }
}
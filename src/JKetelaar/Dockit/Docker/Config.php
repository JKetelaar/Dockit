<?php
/**
 * @author JKetelaar
 */

namespace JKetelaar\Dockit\Docker;

use JKetelaar\Dockit\Common\ConfigHelper;
use JKetelaar\Dockit\Common\DockitCommand;
use JKetelaar\Dockit\Configuration\GlobalConfiguration;
use JKetelaar\Dockit\Configuration\ProjectConfiguration;
use JKetelaar\Dockit\Dockit\ReverseProxy;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
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
        $loader = new Twig_Loader_Filesystem(DOCKIT_RESOURCES_DIR.'/docker/');
        $this->twig = new Twig_Environment($loader);

        $this->createDirectory(getcwd().'/private/dockit/');
        $this->createDirectory($_SERVER['HOME'].'/.dockit/data/mysql');
        $this->createDirectory($_SERVER['HOME'].'/.dockit/dockit/');

        $this->reverseProxy = new ReverseProxy($this);
    }

    /**
     * @param string $directory
     */
    private function createDirectory(string $directory)
    {
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param HelperSet $helperSet
     * @param array ...$parameters
     * @return void
     * @throws \JsonMapper_Exception
     * @throws \ReflectionException
     */
    public function execute(InputInterface $input, OutputInterface $output, HelperSet $helperSet, array $parameters)
    {
        if (!ConfigHelper::hasConfig()) {
            $helper = $helperSet->get('question');

            $projectQuestion = new Question('Name of this project: ', '');
            $projectQuestion->setValidator(
                function ($answer) {
                    if (!is_string($answer) || strlen($answer) <= 2) {
                        throw new \RuntimeException(
                            'The name of the project should have at least 3 characters'
                        );
                    }

                    return $answer;
                }
            );
            $projectName = strtolower($helper->ask($input, $output, $projectQuestion));

            $phpQuestion = new ChoiceQuestion(
                'Please select the PHP version (default "7.1")',
                ['5.6', '7.0', '7.1'],
                '7.1'
            );
            $phpVersion = $helper->ask($input, $output, $phpQuestion);

            $cmsQuestion = new ChoiceQuestion(
                'Please select your CMS (default "Default")',
                ['Default', 'WordPress', 'Typo3', 'Magento1', 'Magento2', 'Symfony'],
                'default'
            );
            $cms = strtolower($helper->ask($input, $output, $cmsQuestion));

            $domainQuestion = new Question('Domain for this project: (default: "'.$projectName.'")"', $projectName);
            $domainQuestion->setValidator(
                function ($answer) {
                    if (!is_string($answer) || strlen($answer) <= 2) {
                        throw new \RuntimeException(
                            'The domain for the project should have at least 3 characters'
                        );
                    }

                    return $answer;
                }
            );
            $domain = strtolower($helper->ask($input, $output, $domainQuestion));

            $config = new ProjectConfiguration();
            $config->setCms($cms);
            $config->setDomain($domain);
            $config->setPhp($phpVersion);
            $config->setProjectName($projectName);

            ConfigHelper::setConfig($config);
        }

        GlobalConfiguration::addProject(getcwd());

        $output->writeln('<info>Creating docker-compose file</info>');
        $this->createDockerCompose();

        $output->writeln('<info>Creating Nginx files</info>');
        $this->createNginx();

        $output->writeln('<info>Creating PHP FPM files</info>');
        $this->createPhpFPM();

        $this->restartHAProxy($output);
    }

    /**
     * @throws \JsonMapper_Exception
     * @throws \ReflectionException
     */
    public function createDockerCompose()
    {
        $mysqlDir = $_SERVER['HOME'].'/.dockit/data/mysql/'.strtolower(
                preg_replace('/[^A-Za-z0-9\-]/', '', $this->getProjectName())
            );

        $this->createTemplateFile(
            'docker-compose.yml.twig',
            getcwd().'/private/dockit/docker-compose.yml',
            [
                'project' => ConfigHelper::getConfig(),
                'directory' => getcwd()
            ]
        );
    }

    /**
     * @return string
     * @throws \JsonMapper_Exception
     * @throws \ReflectionException
     */
    private function getProjectName(): string
    {
        return ConfigHelper::getConfig()->getProjectName();
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
     * @throws \JsonMapper_Exception
     * @throws \ReflectionException
     */
    private function getPHPVersion(): string
    {
        return ConfigHelper::getConfig()->getPhp();
    }

    /**
     * @return string
     * @throws \JsonMapper_Exception
     * @throws \ReflectionException
     */
    private function getProjectCMS(): string
    {
        return ConfigHelper::getConfig()->getCms();
    }

    /**
     * @throws \JsonMapper_Exception
     * @throws \ReflectionException
     */
    public function createNginx()
    {
        $this->createDirectory(getcwd().'/private/dockit/nginx/');

        $this->createTemplateFile(
            'docker/nginx/'.$this->getProjectCMS().'/nginx.conf.twig',
            getcwd().'/private/dockit/nginx/nginx.conf'
        );
    }

    public function createPhpFPM()
    {
        $this->createDirectory(getcwd().'/private/dockit/php-fpm/');

        $this->createTemplateFile(
            'docker/php-fpm/php-ini-overrides.ini.twig',
            getcwd().'/private/dockit/php-fpm/php-ini-overrides.ini'
        );
    }

    /**
     * @param OutputInterface $output
     */
    public function restartHAProxy(OutputInterface $output)
    {
        $this->reverseProxy->createHAProxyConfig();
        $this->reverseProxy->restart($output);
    }

    /**
     * @return Twig_Environment
     */
    public function getTwig(): Twig_Environment
    {
        return $this->twig;
    }
}
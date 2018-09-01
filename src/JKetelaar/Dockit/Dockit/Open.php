<?php
/**
 * @author JKetelaar
 */

namespace JKetelaar\Dockit\Dockit;

use JKetelaar\Dockit\Common\ConfigHelper;
use JKetelaar\Dockit\Common\DockitCommand;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Open
 * @package JKetelaar\Dockit\Dockit
 */
class Open implements DockitCommand
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param HelperSet $helperSet
     * @param array ...$parameters
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output, HelperSet $helperSet, array $parameters)
    {
        try {
            $config = ConfigHelper::getConfig(true);
            if ($config === null) {
                throw new \Exception();
            }

            $url = 'http://'.$config->getDomainWithTLD();
            $output->writeln('<info>Opening '.$url.'</info>');

            \Yuloh\Open\open($url);
        } catch (\Exception $e) {
            $output->writeln('<error>Could not find or read the configuration file</error>');
        }
    }
}
<?php
/**
 * @author JKetelaar
 */

namespace JKetelaar\Dockit\Dockit;

use JKetelaar\Dockit\Common\DockitCommand;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class HAProxy
 * @package JKetelaar\Dockit\Dockit
 */
class HAProxy implements DockitCommand
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
            $url = 'http://haproxy.dockit.site';
            $output->writeln('<info>Opening '.$url.'</info>');

            \Yuloh\Open\open($url);
        } catch (\Exception $e) {
            $output->writeln('<error>Could not find or read the configuration file</error>');
        }
    }
}
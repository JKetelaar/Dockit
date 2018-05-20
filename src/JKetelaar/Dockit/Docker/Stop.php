<?php
/**
 * @author JKetelaar
 */

namespace JKetelaar\Dockit\Docker;

use JKetelaar\Dockit\Common\CommandLine;
use JKetelaar\Dockit\Common\DockitCommand;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Stop
 * @package JKetelaar\Dockit\Docker
 */
class Stop implements DockitCommand
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
        $output->writeln('<info>Stopping docker instances</info>');

        CommandLine::execute('docker-compose --file "' . getcwd() . '/private' . '/dockit/docker-compose.yml" down',
            $output);
    }
}
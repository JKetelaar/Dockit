<?php
/**
 * @author JKetelaar
 */

namespace JKetelaar\Dockit\Docker;

use JKetelaar\Dockit\Common\CommandLine;
use JKetelaar\Dockit\Common\ConfigHelper;
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
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output, HelperSet $helperSet, array $parameters)
    {
        if (ConfigHelper::hasConfig() === false) {
            throw new \Exception('No configuration found; execute `dockit config` first');
        }

        $output->writeln('<info>Stopping docker instances</info>');

        CommandLine::execute(
            'docker-compose --log-level ERROR --file "'.getcwd().'/private'.'/dockit/docker-compose.yml" down',
            $output
        );

        $output->writeln('<info>Stopping old docker instances</info>');
        CommandLine::execute(
            'docker ps --filter "status=running" | grep \'days ago\' | awk \'{print $1}\' | xargs docker stop'
        );

        $output->writeln('<info>Removing old docker instances</info>');
        CommandLine::execute(
            'docker ps --filter "status=exited" | grep \'weeks ago\' | awk \'{print $1}\' | xargs docker rm'
        );
    }
}
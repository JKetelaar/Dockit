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
 * Class Start
 * @package JKetelaar\Dockit\Docker
 */
class Start implements DockitCommand
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

        $output->writeln('<info>Starting docker instances</info>');

        CommandLine::execute(
            'docker-compose --log-level ERROR --file "'.getcwd(
            ).'/private'.'/dockit/docker-compose.yml" up --no-recreate -d',
            $output
        );
    }
}
<?php

use Silly\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

const DOCKIT_VERSION = '0.0.1';
const DOCKIT_RESOURCES_DIR = __DIR__.'/../resources';

if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    require __DIR__.'/../vendor/autoload.php';
} else {
    require __DIR__.'/../../../autoload.php';
}

/* Application initiation */
$app = new Application('Dockit', DOCKIT_VERSION);

$dockerStart = new \JKetelaar\Dockit\Docker\Start();
$dockerStop = new \JKetelaar\Dockit\Docker\Stop();
$dockerConfig = new \JKetelaar\Dockit\Docker\Config();

function addEmptyCommand(
    \JKetelaar\Dockit\Common\DockitCommand $command,
    string $name,
    string $description,
    Application $app
) {
    $app->command(
        $name,
        function (
            InputInterface $input,
            OutputInterface $output
        ) use ($command) {
            $command->execute($input, $output, $this->getHelperSet(), []);
        }
    )->descriptions(
        $description,
        []
    );
}

addEmptyCommand(
    new \JKetelaar\Dockit\Dockit\Open(),
    'open',
    'Creates the configuration files for the docker instances of the current project',
    $app
);

addEmptyCommand(
    new \JKetelaar\Dockit\Dockit\HAProxy(),
    'haproxy',
    'Creates the configuration files for the docker instances of the current project',
    $app
);

$app->command(
    'config [--force]',
    function (
        $force,
        InputInterface $input,
        OutputInterface $output
    ) use ($dockerConfig) {
        $dockerConfig->execute($input, $output, $this->getHelperSet(), ['force' => $force]);
    }
)->descriptions(
    'Creates the configuration files for the docker instances of the current project',
    ['--force' => 'Reset the configuration']
);

$app->command(
    'start',
    function (
        InputInterface $input,
        OutputInterface $output
    ) use ($dockerStart, $dockerConfig) {
        $dockerStart->execute($input, $output, $this->getHelperSet(), []);

        $dockerConfig->restartHAProxy($output);
    }
)->descriptions(
    'Starts the docker instances for the current project',
    []
);

$app->command(
    'stop [--all]',
    function (
        $all,
        InputInterface $input,
        OutputInterface $output
    ) use ($dockerStop, $dockerConfig) {
        $dockerStop->execute($input, $output, $this->getHelperSet(), ['all' => $all]);

        $dockerConfig->restartHAProxy($output);
    }
)->descriptions(
    'Stops the Docker instances for the current project',
    ['--all' => 'Also stop all other running Docker containers']
);

$app->command(
    'restart',
    function (
        InputInterface $input,
        OutputInterface $output
    ) use ($dockerStart, $dockerStop, $dockerConfig) {
        if (\JKetelaar\Dockit\Common\ConfigHelper::hasConfig() === true) {
            $dockerStop->execute($input, $output, $this->getHelperSet(), []);
            $dockerStart->execute($input, $output, $this->getHelperSet(), []);
        } else {
            $output->writeln('<error>No configuration found, only restarting Dockit system itself</error>');
        }

        $dockerConfig->restartHAProxy($output);
    }
)->descriptions(
    'Restarts the docker instances for the current project',
    []
);

/**
 * @noinspection PhpUnhandledExceptionInspection
 * Because we do want to display the error through Symfony Console and not handle it ourselves
 */
$app->run();
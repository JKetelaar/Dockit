<?php

use Silly\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

const DOCKIT_VERSION = '0.0.1';

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
} else {
    require __DIR__ . '/../../../autoload.php';
}

/* Application initiation */
$app = new Application('Dockit', DOCKIT_VERSION);

$dockerStart = new \JKetelaar\Dockit\Docker\Start();
$dockerStop = new \JKetelaar\Dockit\Docker\Stop();
$dockerConfig = new \JKetelaar\Dockit\Docker\Config();

$app->command(
    'config',
    function (
        InputInterface $input,
        OutputInterface $output
    ) use ($dockerConfig) {
        $dockerConfig->execute($input, $output, $this->getHelperSet(), []);
    }
)->descriptions(
    'Creates the configuration files for the docker instances of the current project',
    []
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
    'stop',
    function (
        InputInterface $input,
        OutputInterface $output
    ) use ($dockerStop, $dockerConfig) {
        $dockerStop->execute($input, $output, $this->getHelperSet(), []);

        $dockerConfig->restartHAProxy($output);
    }
)->descriptions(
    'Stops the docker instances for the current project',
    []
);

$app->command(
    'restart',
    function (
        InputInterface $input,
        OutputInterface $output
    ) use ($dockerStart, $dockerStop, $dockerConfig) {
        $dockerStop->execute($input, $output, $this->getHelperSet(), []);
        $dockerStart->execute($input, $output, $this->getHelperSet(), []);

//        $dockerConfig->restartHAProxy($output);
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
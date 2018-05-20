<?php
/**
 * @author JKetelaar
 */

namespace JKetelaar\Dockit\Common;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CommandLine
 * @package JKetelaar\Dockit\Common
 */
class CommandLine
{
    /**
     * @param string $command
     * @param OutputInterface|null $output
     *
     * @return string|string
     */
    public static function execute(string $command, OutputInterface $output = null): ?string
    {
        if (($fp = popen($command, 'r'))) {
            while (!feof($fp)) {
                $fOutput = fread($fp, 1024);
                if ($output !== null) {
                    $output->writeln('<info>' . $fOutput . '</info>');
                }
                flush();
            }
            fclose($fp);

            if ($output === null) {
                return $output;
            }

            return null;
        } else {
            throw new \InvalidArgumentException('Cannot execute command');
        }
    }
}
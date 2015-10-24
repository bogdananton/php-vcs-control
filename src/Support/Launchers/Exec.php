<?php
namespace PHPVCSControl\Support\Launchers;

use PHPVCSControl\Support\Filesystem;
use Psr\Log\LoggerInterface;

class Exec implements LauncherInterface
{
    /** @var \Monolog\Logger */
    protected $logger;

    /** @var Filesystem */
    protected $filesystem;

    public function __construct(LoggerInterface $logger, Filesystem $filesystem)
    {
        $this->logger = $logger;
        $this->filesystem = $filesystem;
    }

    public function run($command)
    {
        exec($command, $output, $return);
        $this->logger->addDebug('executing command: ' . $command);

        return $output;
    }

    public function getFilesystem()
    {
        return $this->filesystem;
    }

    public function getLogger()
    {
        return $this->logger;
    }
}

<?php
namespace PHPVCSControl\Support\Launchers;

use PHPVCSControl\Support\Filesystem;
use Psr\Log\LoggerInterface;

interface LauncherInterface
{
    public function __construct(LoggerInterface $logger, Filesystem $filesystem);

    /**
     * @param string $command
     * @return string
     */
    public function run($command);

    /**
     * @todo check if the filesystem should be injected separately throughout the application
     * @return Filesystem
     */
    public function getFilesystem();

    /**
     * @todo check if the logger should be injected separately throughout the application
     * @return LoggerInterface
     */
    public function getLogger();
}

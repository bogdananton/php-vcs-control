<?php
namespace PHPVCSControl\Git;

use PHPVCSControl\ValueObjects\Filesystem\Filepath;
use PHPVCSControl\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /** @var array */
    protected $settings;

    /** @var Filepath */
    protected $repoPath;

    public function __construct(Filepath $repoPath)
    {
        $this->repoPath = $repoPath;

        $configFilePath = Filepath::fromNative(implode(DIRECTORY_SEPARATOR, [$this->repoPath, '.git', 'config']));
        $contents = parse_ini_file($configFilePath, true);

        if (count($contents) === 0 || !file_exists($configFilePath)) {
            throw new \InvalidArgumentException('The configuration is invalid / missing.');
        }

        /** @todo interpret these settings after adding more VCS types */
        $this->settings = parse_ini_file($configFilePath, true);
    }

    public function toArray()
    {
        return $this->settings;
    }

    public function toJSON()
    {
        return json_encode($this->toArray());
    }
}

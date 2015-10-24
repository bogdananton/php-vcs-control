<?php
namespace PHPVCSControl;


use PHPVCSControl\ValueObjects\Filesystem\Filepath;

interface ConfigurationInterface
{
    public function __construct(Filepath $repoPath);

    public function toArray();

    public function toJSON();
}

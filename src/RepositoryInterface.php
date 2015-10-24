<?php
namespace PHPVCSControl;

use Doctrine\Common\Collections\ArrayCollection;
use PHPVCSControl\Support\Launchers\LauncherInterface;
use PHPVCSControl\ValueObjects\Filesystem\Filepath;
use PHPVCSControl\ValueObjects\RepositorySourceInterface;
use PHPVCSControl\ValueObjects\StringLiteral\StringLiteral;

interface RepositoryInterface
{
    public function __construct(LauncherInterface $launcher, Filepath $executablePath, RepositorySourceInterface $source, StringLiteral $branch, Filepath $repoPath);

    /**
     * @param LauncherInterface $launcher
     * @param string $executablePath
     * @param string $source
     * @param string $branch
     * @param string $repoPath
     *
     * @return static
     */
    public static function build(LauncherInterface $launcher, $executablePath, $source, $branch, $repoPath);

    /**
     * Clone the repository in the repoPath. If the repoPath exists then destroy (remove) the folder before cloning.
     * @return mixed
     */
    public function cloneRepository();

    /**
     * Checks if the repoPath contains the cloned repository.
     * @return boolean
     */
    public function isDeployed();

    /**
     * @throws \InvalidArgumentException when the configuration couldn't be loaded because is missing or invalid.
     * @return ConfigurationInterface
     */
    public function getConfiguration();

    /**
     * @return ArrayCollection
     */
    public function commits();

    /**
     * Will delete the deployed repository's folder
     */
    public function destroy();

    public function fetch();
    public function checkout($branchOrSha);
    public function pull($branchOrSha = 'HEAD');
}


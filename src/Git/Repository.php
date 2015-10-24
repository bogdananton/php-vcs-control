<?php
namespace PHPVCSControl\Git;

use Doctrine\Common\Collections\ArrayCollection;
use PHPVCSControl\Support\Launchers\LauncherInterface;
use PHPVCSControl\ValueObjects\Filesystem\Filepath;
use PHPVCSControl\ValueObjects\RepositorySourceInterface;
use PHPVCSControl\ValueObjects\StringLiteral\StringLiteral;
use PHPVCSControl\ValueObjects\ValueObjectFactory as value;
use PHPVCSControl\ValueObjects\Web\Url;
use PHPVCSControl\RepositoryInterface;

class Repository implements RepositoryInterface
{
    /** @var  LauncherInterface */
    protected $launcher;

    /** @var  Filepath */
    protected $gitExecutablePath;

    /** @var Filepath|Url */
    protected $source;

    /** @var StringLiteral */
    protected $branchName;

    /** @var Filepath */
    protected $repoPath;

    public function __construct(LauncherInterface $launcher, Filepath $gitExecutablePath, RepositorySourceInterface $source, StringLiteral $branch, Filepath $repoPath)
    {
        $this->launcher = $launcher;

        $this->gitExecutablePath = $gitExecutablePath;
        $this->source = $source;
        $this->branchName = $branch;
        $this->repoPath = $repoPath;
    }

    public function cloneRepository()
    {
        if ($this->isDeployed()) {
            $this->destroy();
        }

        $commandParts = [];
        $commandParts['executable'] = (string)$this->gitExecutablePath;
        $commandParts['action'] = 'clone --quiet --no-hardlinks';
        $commandParts['local'] = ($this->source instanceof Filepath) ? '--local' : '';
        $commandParts['branch'] = '--branch ' . $this->branchName;
        $commandParts['source'] = '"' . $this->source . '"';
        $commandParts['destination'] = '"' . $this->repoPath . '"';

        $commandString = implode(' ', $commandParts);
        return $this->launcher->run($commandString);
    }

    public function destroy()
    {
        $this->launcher->getFilesystem()->rmdir($this->repoPath, true); // delete the folder
    }

    public function getConfiguration()
    {
        return new Configuration($this->repoPath);
    }

    public function isDeployed()
    {
        try {
            $this->getConfiguration(); // throws exception when the configuration is invalid.
            return true;

        } catch (\Exception $e) {
            $this->launcher->getLogger()->error(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __METHOD__ . ' has thrown ' . $e->getMessage() . ' [' . $e->getCode() . ']');
            return false;
        }
    }

    private function hasFlag($flag, $flags)
    {
        $result = $flags - $flag;
        return (($result & ($result - 1)) === 0);
    }

    /**
     * @inheritdoc
     * @todo change since and flags to array of options after implementing another VCS type.
     */
    public function commits($since = '', $flags = 0)
    {
        // if (!$this->isDeployed()) {
        //    // @todo throw exception or clone
        // }

        $commandParts = [];
        $commandParts['executable'] = (string)$this->gitExecutablePath;
        $commandParts['remotePath'] = '-C "' . $this->repoPath . '"';
        $commandParts['action'] = 'log --date=iso --numstat';
        $commandParts['option-no-merges'] = $this->hasFlag(VCS_FLAG_NO_MERGES, $flags) ? '--no-merges' : '';
        $commandParts['option-no-walk'] = $this->hasFlag(VCS_FLAG_NO_WALK, $flags) ? '--no-walk' : '';
        $commandParts['option-tags'] = $this->hasFlag(VCS_FLAG_TAGS, $flags) ? '--tags' : '';
        $commandParts['format'] = '--pretty=format:"{\"commit\":\"%H\",\"committer_name\":\"%cN\",\"committer_email\":\"%cE\",\"committer_date\":\"%ci\",\"subject\":\"%s\"}"';
        $commandParts['since'] = (strlen($since) > 0) ? '--since="' . $since . '"' : '';

        $commandString = implode(' ', array_filter($commandParts));
        $extractListing = new ArrayCollection($this->launcher->run($commandString));
        $responseListing = new ArrayCollection();

        $state = [];

        $extractListing->forAll(function ($index, $item) use ($responseListing, &$state) {
            switch (true) {
                case trim($item) === '':
                    if (!empty($state)) {
                        $responseListing->add(Commit::build($state));
                    }
                    break;

                case substr($item, 0, 1) === '{':
                    $state = json_decode($item, true);
                    $state['changes'] = [];
                    break;

                default:
                    $parts = explode("\t", $item);
                    if (count($parts) === 3) {
                        $state['changes'][$parts[2]] = [
                            '+' => $parts[0],
                            '-' => $parts[1]
                        ];
                    }
                    break;
            }

            return true;
        });

        return $responseListing;
    }

    public function fetch()
    {
        $commandParts = [];
        $commandParts['executable'] = (string)$this->gitExecutablePath;
        $commandParts['remotePath'] = '-C "' . $this->repoPath . '"';
        $commandParts['action'] = 'fetch --all --quiet';

        $commandString = implode(' ', $commandParts);
        return $this->launcher->run($commandString);
    }

    public function checkout($branchOrSha = 'HEAD')
    {
        $commandParts = [];
        $commandParts['executable'] = (string)$this->gitExecutablePath;
        $commandParts['remotePath'] = '-C "' . $this->repoPath . '"';
        $commandParts['action'] = 'checkout ' . $branchOrSha . ' --quiet';

        $commandString = implode(' ', $commandParts);
        return $this->launcher->run($commandString);
    }

    public function pull($branchOrSha = 'HEAD')
    {
        $commandParts = [];
        $commandParts['executable'] = (string)$this->gitExecutablePath;
        $commandParts['remotePath'] = '-C "' . $this->repoPath . '"';
        $commandParts['action'] = 'pull ' . $branchOrSha . ' --quiet';

        $commandString = implode(' ', $commandParts);
        return $this->launcher->run($commandString);
    }

    /**
     * @inheritdoc
     * @return self
     */
    public static function build(LauncherInterface $launcher, $exePathString, $sourceString, $branchString, $pathString)
    {
        $sourceType = filter_var($sourceString, FILTER_VALIDATE_URL) ? value::URL : value::FILEPATH;

        $exePath = value::get($exePathString, value::FILEPATH); /** @var Filepath $exePath */
        $source = value::get($sourceString, $sourceType); /** @var Filepath|Url $source */
        $branch = value::get($branchString, value::FILEPATH); /** @var Filepath $branch */
        $path = value::get($pathString, value::FILEPATH); /** @var Filepath $path */

        return new self($launcher, $exePath, $source, $branch, $path);
    }
}

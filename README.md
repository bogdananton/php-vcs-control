**php-vcs-control**'s purpose is to provide a basic way to interact with VCS repositories. (Read-only mode only, to be used for extracting logs.)

----

**Getting started**

```php
// prepare demo paths
$exePath = '/usr/bin/git';
$source = 'https://github.com/bogdananton/php-vcs-control';
$path = '/projects/demo-php-vcs-control-repo/';
$branch = 'master';

// prepare logger
$logger = new \Monolog\Logger('console');
$logger->pushHandler(new \Monolog\Handler\StreamHandler(fopen('php://stderr', 'w')));

$launcherEngine = new \PHPVCSControl\Support\Launchers\Exec($logger, new \PHPVCSControl\Support\Filesystem());
$demoRepository = \PHPVCSControl\Git\Repository::build($launcherEngine, $exePath, $source, $branch, $path);
```

**Available commands**

```
$repo->cloneRepository()
$repo->commits()
$repo->destroy()
$repo->fetch()
$repo->checkout($branchOrSha)
$repo->pull($branchOrSha)

isDeployed()
getConfiguration()
```


**Sample commands**

> check bin/console.php for usage

```php
// Clone repository
$logger->addInfo(sprintf('Cloning demo repository [%s]', $source));
$demoRepository->cloneRepository();

// Check if the repository is deployed
$isDeployed = $demoRepository->isDeployed();
$logger->addInfo(sprintf('Is the repository deployed? [%s]', $isDeployed ? 'yes' : 'no'));

// Get the repository's configuration
$logger->addInfo('Get configuration: ' . $demoRepository->getConfiguration()->toJSON());

// Fetch
$demoRepository->fetch();
$logger->addInfo('Fetched');

// Get the commits
$commitCollection = $demoRepository->commits('2000-01-01', VCS_FLAG_NO_MERGES + VCS_FLAG_NO_WALK + VCS_FLAG_TAGS);
$logger->addInfo('Get commit count: ' . $commitCollection->count());

// List commits' details
$commitCollection->forAll(function ($i, $item) use ($logger) {
    $logger->info($item->sha . ' [' . $item->date . '] [lines added: ' . $item->linesAdded . '] [lines removed: ' . $item->linesRemoved . ']');
    return true;
});

// Checkout to a commit ID
$index = mt_rand(0, $commitCollection->count() - 1);
$commitItem = $commitCollection->get($index);

$demoRepository->checkout($commitItem->sha);
$logger->addInfo('Checkout repo to commit index [' . $index . '], with commitID [' . $commitItem->sha . '] and subject [' . $commitItem->subject . ']');

```

**Available VCS**

Currently only GIT is supported, with SVN to be added soon.

**@todo**

* convert the commit from a data structure to a model.
* test pull and checkout from multiple origins
* implement $repo->status
* improve $repo->commit() filters
* add support for SVN
* check if filesystem and logger should be coupled with launcher or injected
* check and fix Support\Launchers\ProcOpen
* add an environment checker (ex: for running exec)

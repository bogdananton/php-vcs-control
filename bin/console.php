<?php

if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    die('Run "composer update" in the root folder!' . PHP_EOL . PHP_EOL);
}

require_once __DIR__ . '/../vendor/autoload.php';

// prepare demo paths
$exePath = '/usr/bin/git';
$source = '/projects/MySQL-to-object-mapper';
$path = '/run/media/bogdan/My Passport/up/';
$branch = 'master';

// prepare logger
$logger = new \Monolog\Logger('console');
$logger->pushHandler(new \Monolog\Handler\StreamHandler(fopen('php://stderr', 'w')));

$launcherEngine = new \PHPVCSControl\Support\Launchers\Exec($logger, new \PHPVCSControl\Support\Filesystem());
$demoRepository = \PHPVCSControl\Git\Repository::build($launcherEngine, $exePath, $source, $branch, $path);

//////////////////////////////////////////////////
// Do sample actions
//////////////////////////////////////////////////

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


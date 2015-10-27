<?php
namespace PHPVCSControl\Support;

/**
 * @note the logger should be injected in the FS so that errors / warnings / debug / info messages will be displayed
 */
class Filesystem extends \Symfony\Component\Filesystem\Filesystem
{
    public function rmdir($path, $removeDirPath = false)
    {
        if (file_exists($path)) {
            foreach (new \DirectoryIterator($path) as $fileInfo) {
                switch (true) {
                    case $fileInfo->isFile():
                        @unlink($fileInfo->getPathname());
                        break;

                    case $fileInfo->isDir() && !$fileInfo->isDot():
                        $this->rmdir($fileInfo->getPathname(), true);
                        break;

                    default:
                        break;
                }
            }

            if ($removeDirPath) {
                rmdir($path);
            }
        }
    }

    public function getFileContents($path)
    {
        if (file_exists($path)) {
            return file_get_contents($path);
        }
        return '';
    }

    public function isdir($path)
    {
        return is_dir($path);
    }
}

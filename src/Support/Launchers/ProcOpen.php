<?php
namespace PHPVCSControl\Support\Launchers;

use PHPVCSControl\Support\Filesystem;
use Psr\Log\LoggerInterface;

class ProcOpen implements LauncherInterface
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

    protected $envopts = [];

    public function run($command)
    {
        $descriptorspec = [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $env = null;
        $pipes = [];

        if (count($_ENV) === 0) {
            foreach ($this->envopts as $k => $v) {
                putenv(sprintf("%s=%s", $k, $v));
            }

        } else {
            $env = array_merge($_ENV, $this->envopts);
        }

        $resource = proc_open($command, $descriptorspec, $pipes, $cwd, $env);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);

        foreach ($pipes as $pipe) {
            fclose($pipe);
        }

        $status = trim(proc_close($resource));
        if ($status) {
            throw new \Exception($stderr);
        }
        return $stdout;
    }

    /**
     * Sets custom environment options.
     *
     * @param string $key
     * @param string $value
     */
    public function setenv($key, $value) {
        $this->envopts[$key] = $value;
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

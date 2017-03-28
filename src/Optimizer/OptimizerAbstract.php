<?php
namespace Itgalaxy\Imagemin\Optimizer;

use Itgalaxy\OsFilter\OsFilter;
use Itgalaxy\Imagemin\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\ExecutableFinder;

abstract class OptimizerAbstract implements OptimizerInterface
{
    protected $options = [];

    protected $fs = null;

    public function __construct(array $options = [])
    {
        // Todo merge options

        $this->fs = new Filesystem();
    }

    abstract public function optimize($input);

    protected function execute($input, $bin, array $args = [])
    {
        $tempDir = $this->fs->getTempDir();

        // tempnam doesn't not work correctly with temp directories
        $inputPath = $this->fs->tempnam($tempDir, 'imagemin-');
        $outputPath = $this->fs->tempnam($tempDir, 'imagemin-');

        fwrite($input, $inputPath);

        $originalInputUri = stream_get_meta_data($input)['uri'];

        $this->fs->copy($originalInputUri, $inputPath, true);

        foreach ($args as &$arg) {
            if (strpos($arg, '${input}') !== false) {
                $arg = str_replace('${input}', $inputPath, $arg);
            } else if (strpos($arg, '${output}') !== false) {
                $arg = str_replace('${output}', $outputPath, $arg);
            }
        }

        $builder = new ProcessBuilder();
        $process = $builder
            ->setPrefix($bin)
            ->setArguments($args)
            ->getProcess();

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Remove temporary input path
        $this->fs->remove($inputPath);

        return fopen($outputPath, 'r');
    }

    private function resolveDefault($default)
    {
        return is_callable($default) ? call_user_func($default) : $default;
    }

    private function option($name, $default = null)
    {
        return isset($this->options[$name]) ? $this->options[$name] : $this->resolveDefault($default);
    }

    // Todo add pre testing
    // Todo merge with *Bin classes
    private function findExecutable($name)
    {
        $executableFinder = new ExecutableFinder();

        return $this->option($name . '_bin', function () use ($name, $executableFinder) {
            if (isset($this->options['buildInBin'])) {
                $osFilter = new OsFilter();
                $foundBins = $osFilter->find($this->options['buildInBin']);

                // why is_array
                if (is_array($foundBins) && count($foundBins) > 0) {
                    $foundBin = current($foundBins);
                    $binPath = BIN_DIR . '/' . $name . '/' . $foundBin['path'];

                    return $binPath;
                }
            }

            return $executableFinder->find($name, $name);
        });
    }
}

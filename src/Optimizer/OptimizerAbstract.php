<?php
namespace Itgalaxy\Imagemin\Optimizer;

use Itgalaxy\OsFilter\OsFilter;
use Itgalaxy\Imagemin\Filesystem\Filesystem;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\ExecutableFinder;

abstract class OptimizerAbstract implements OptimizerInterface
{
    protected $binPath = null;

    protected $options = [];

    protected $fs = null;

    public function __construct(array $options = [])
    {
        $this->fs = new Filesystem();

        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $this->options = $optionsResolver->resolve($options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        // Nothing
    }

    abstract public function optimize($input);

    protected function execute($input, $bin, array $args = [], $onlyInput = false)
    {
        $tempDir = $this->fs->getTempDir();

        // tempnam doesn't not work correctly with temp directories
        $inputPath = $this->fs->tempnam($tempDir, 'imagemin-');
        $writeResult = @fwrite($input, $inputPath);

        if ($writeResult === false) {
            throw new \Exception('Can\'t write stream by ' . $inputPath);
        }

        $outputPath = !$onlyInput ? $this->fs->tempnam($tempDir, 'imagemin-') : null;

        $originalInputUri = stream_get_meta_data($input)['uri'];

        $this->fs->copy($originalInputUri, $inputPath, true);

        foreach ($args as &$arg) {
            if (strpos($arg, '${input}') !== false) {
                $arg = str_replace('${input}', $inputPath, $arg);
            } else if (!$onlyInput && strpos($arg, '${output}') !== false) {
                $arg = str_replace('${output}', $outputPath, $arg);
            }
        }

        $builder = new ProcessBuilder();
        $process = $builder
            ->setPrefix($bin)
            ->setArguments($args)
            ->getProcess();

        // Disable output to save memory
        $process->disableOutput();
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Remove temporary input path
        if (!$onlyInput) {
            $this->fs->remove($inputPath);
        }

        return fopen($onlyInput ? $inputPath : $outputPath, 'r');
    }
}

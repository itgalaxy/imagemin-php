<?php
namespace Itgalaxy\Imagemin\Optimizer;

use Itgalaxy\Imagemin\Bin\PngoutBin;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;

class PngoutOptimizer extends OptimizerAbstract implements OptimizerInterface
{
    protected $binPath = null;

    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $binWrapper = new PngoutBin();
        $this->binPath = $binWrapper->getBinPath();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'strategy' => null
        ]);

        $resolver->setAllowedTypes('strategy', ['null', 'int']);
        $resolver->setAllowedValues('strategy', function ($value) {
            return  ($value === null) || ($value >= 0 && $value <= 4);
        });
    }

    public function optimize($input)
    {
        if (!is_resource($input)) {
            throw new \Exception('Expected a resource type');
        }

        if (!$this->fs->isPng($input)) {
            return $input;
        }

        $args = [];

        $options = $this->options;

        array_push($args, '-');
        array_push($args, '-');
        array_push($args, '-y');
        array_push($args, '-force');

        if (is_numeric($options['strategy'])) {
            array_push($args, '-s' . $options['strategy']);
        }

        $builder = new ProcessBuilder();
        $process = $builder
            ->setPrefix($this->binPath)
            ->setArguments($args)
            ->getProcess();

        $process->setInput($input);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $outputContents = $process->getOutput();

        $tempDir = $this->fs->getTempDir();
        $outputPath = $this->fs->tempnam($tempDir, 'imagemin-');

        $stream = @fopen($outputPath, 'w+');

        if ($stream === false) {
            throw new \Exception('Can\'t open stream by ' . $outputPath);
        }

        $writeResult = @fwrite($stream, $outputContents);

        if ($writeResult === false) {
            throw new \Exception('Can\'t write stream by ' . $outputPath);
        }

        return $stream;
    }
}

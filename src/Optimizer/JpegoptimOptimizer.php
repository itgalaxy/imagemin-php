<?php
namespace Itgalaxy\Imagemin\Optimizer;

use Itgalaxy\Imagemin\Bin\JpegoptimBin;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;

class JpegoptimOptimizer extends OptimizerAbstract implements OptimizerInterface
{
    protected $binPath = null;

    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $binWrapper = new JpegoptimBin();
        $this->binPath = $binWrapper->getBinPath();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'progressive' => null,
            'max' => null,
            'size' => null
        ]);
        $resolver->setAllowedTypes('progressive', ['null', 'bool']);
        $resolver->setAllowedTypes('max', ['null', 'int']);
        $resolver->setAllowedValues('max', function ($value) {
            return  ($value === null) || (is_numeric($value) && $value >= 0 && $value <= 100);
        });
        $resolver->setAllowedTypes('size', ['null', 'int', 'string']);
    }

    public function optimize($input)
    {
        if (!is_resource($input)) {
            throw new \Exception('Expected a resource type');
        }

        if (!$this->fs->isJPG($input)) {
            return $input;
        }

        $args = [
            '--strip-all',
            '--strip-iptc',
            '--strip-icc',
            '--stdin',
            '--stdout'
        ];

        $options = $this->options;

        if ($options['progressive']) {
            array_push($args, '--all-progressive');
        }

        if ($options['max'] || is_numeric($options['max'])) {
            array_push($args, '--max=' . $options['max']);
        }

        if ($options['size'] || is_numeric($options['size']) || is_string($options['size'])) {
            array_push($args, '--size=' . $options['size']);
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

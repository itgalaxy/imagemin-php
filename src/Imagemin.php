<?php
namespace Itgalaxy\Imagemin;

use Itgalaxy\Imagemin\Filesystem\Filesystem;
use Itgalaxy\Imagemin\Optimizer\NullOptimizer;
use Itgalaxy\Imagemin\Optimizer\OptimizerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Imagemin
{
    // Todo fs private or protected
    private $fs = null;

    private $options = [];

    public function __construct(array $options = [], LoggerInterface $logger = null)
    {
        $this->fs = new Filesystem();
        $this->logger = $logger ? $logger : new NullLogger();

        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $this->options = $optionsResolver->resolve($options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('plugins', new NullOptimizer());
        $resolver->setAllowedTypes('plugins', OptimizerInterface::class);
    }

    // Todo need support glob as $input
    public function process($input, $output = null, array $options = [])
    {
        if (is_string($input)) {
            $input = [$input];
        }

        if (count($options) > 0) {
            $optionsResolver = new OptionsResolver();
            $this->configureOptions($optionsResolver);
            $options = $optionsResolver->resolve($options);
        } else {
            $options = $this->options;
        }

        $result = [];

        foreach($input as $filePath) {
            $result[$filePath] = $this->handleFile($filePath, $output, $options);
        }

        return $result;
    }

    protected function handleFile($input, $output, $options)
    {
        $optimizer = $options['plugins'];

        if (!$this->fs->exists($input)) {
            throw new \Exception('File ' . $input . ' is not found');
        }

        $srcStream = fopen($input, 'r');

        if ($srcStream === false) {
           throw new \Exception('Failed to open stream' . $input);
        }

        $optimizedStream = $optimizer->optimize($srcStream);

        $srcSize = fstat($srcStream)['size'];
        $optimizedSize = fstat($optimizedStream)['size'];

        $stream = $optimizedSize > $srcSize ? $srcStream : $optimizedStream;

        // Todo remove min-
        $dest = $output ? $output . '/min-' . basename($input) : null;

        if (!isset($dest)) {
            return $stream;
        }

        $streamUri = stream_get_meta_data($stream)['uri'];

        try {
            $this->fs->copy($streamUri, $dest, true);
        } finally {
            // Anyway we should remove temporary optimized file
            $this->fs->remove($streamUri);
        }

        return $stream;
    }
}

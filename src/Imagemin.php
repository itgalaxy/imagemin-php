<?php
namespace Itgalaxy\Imagemin;

use Itgalaxy\Imagemin\Filesystem\Filesystem;
use Itgalaxy\Imagemin\Optimizer\NullOptimizer;
use Itgalaxy\Imagemin\Optimizer\OptimizerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Webmozart\Glob\Iterator\GlobIterator;

class Imagemin
{
    private $fs = null;

    private $options = [];

    public function __construct(array $options = [])
    {
        $this->fs = new Filesystem();

        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $this->options = $optionsResolver->resolve($options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'plugins' => new NullOptimizer()
        ]);
        $resolver->setAllowedTypes('plugins', OptimizerInterface::class);
    }

    public function process($input, $output = null)
    {
        if (!is_string($input)) {
            throw new \Exception('Input should be string');
        }

        $result = [];

        $iterator = new GlobIterator($input);

        foreach ($iterator as $path) {
            // Todo check flags for all fopen
            $input = @fopen($path, 'r');

            if ($input === false) {
                throw new \Exception('Failed to open stream' . $input);
            }

            $result[$path] = $this->handleFile($input, $output);
        }

        return $result;
    }

    public function processStream($input)
    {
        $output = $this->fs->getTempDir();

        return $this->handleFile($input, $output);
    }

    protected function handleFile($input, $output)
    {
        $optimizer = $this->options['plugins'];

        $optimizedStream = $optimizer->optimize($input);

        $srcSize = fstat($input)['size'];
        $optimizedSize = fstat($optimizedStream)['size'];

        $stream = $optimizedSize > $srcSize ? $input : $optimizedStream;

        $uri = stream_get_meta_data($input)['uri'];
        $dest = $output ? $output . '/' . basename($uri) : null;

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

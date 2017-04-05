<?php
namespace Itgalaxy\Imagemin\Optimizer;

use Itgalaxy\Imagemin\Bin\GifsicleBin;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GifsicleOptimizer extends OptimizerAbstract implements OptimizerInterface
{
    protected $binPath = null;

    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $binWrapper = new GifsicleBin();
        $this->binPath = $binWrapper->getBinPath();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'interlaced' => null,
            'optimizationLevel' => 1,
            'colors' => null
        ]);
        $resolver->setAllowedTypes('interlaced', ['null', 'bool']);
        $resolver->setAllowedTypes('optimizationLevel', ['null', 'int']);
        $resolver->setAllowedValues('optimizationLevel', function ($value) {
            return ($value === null) || $value >= 1 && $value <= 3;
        });
        $resolver->setAllowedTypes('colors', ['null', 'int']);
        $resolver->setAllowedValues('colors', function ($value) {
            return ($value === null) || ($value >= 2 && $value <= 256);
        });
    }

    public function optimize($input)
    {
        if (!is_resource($input)) {
            throw new \Exception('Expected a resource type');
        }

        if (!$this->fs->isGIF($input)) {
            return $input;
        }

        $options = $this->options;

        $args = [
            '--no-warnings'
        ];

        if ($options['interlaced']) {
            array_push($args, '--interlace');
        }
        if ($options['optimizationLevel']) {
            array_push($args, '--optimize=' . $options['optimizationLevel']);
        }

        if ($options['colors']) {
            array_push($args, '--colors=' . $options['colors']);
        }

        array_push($args, '--output');
        array_push($args, '${output}');
        array_push($args, '${input}');

        return $this->execute($input, $this->binPath, $args);
    }
}

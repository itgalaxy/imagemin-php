<?php
namespace Itgalaxy\Imagemin\Optimizer;

use Itgalaxy\Imagemin\Bin\OptipngBin;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OptipngOptimizer extends OptimizerAbstract implements OptimizerInterface
{
    protected $binPath = null;

    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $binWrapper = new OptipngBin();
        $this->binPath = $binWrapper->getBinPath();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'optimizationLevel' => 3,
            'bitDepthReduction' => true,
            'colorTypeReduction' => true,
            'paletteReduction' => true
        ]);

        $resolver->setAllowedTypes('optimizationLevel', ['int']);
        $resolver->setAllowedValues('optimizationLevel', function ($value) {
            return $value >= 0 && $value <= 7;
        });
        $resolver->setAllowedTypes('bitDepthReduction', ['null', 'bool']);
        $resolver->setAllowedTypes('colorTypeReduction', ['null', 'bool']);
        $resolver->setAllowedTypes('paletteReduction', ['null', 'bool']);
    }

    public function optimize($input)
    {
        if (!is_resource($input)) {
            throw new \Exception('Expected a resource type');
        }

        if (!$this->fs->isPNG($input)) {
            return $input;
        }

        $options = $this->options;

        $optimizationLevel = $options['optimizationLevel'];

        $args = [
            '-strip', 'all',
            '-clobber',
            '-o', $optimizationLevel,
            '-out', '${output}'
        ];

        if ($options['bitDepthReduction']) {
            array_push($args, '-nb');
        }

        if ($options['colorTypeReduction']) {
            array_push($args, '-nc');
        }

        if ($options['paletteReduction']) {
            array_push($args, '-np');
        }

        array_push($args, '${input}');

        return $this->execute($input, $this->binPath, $args);
    }
}

<?php
namespace Itgalaxy\Imagemin\Optimizer;

use Itgalaxy\Imagemin\Bin\ZopflipngBin;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ZopfliOptimizer extends OptimizerAbstract implements OptimizerInterface
{
    protected $binPath = null;

    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $binWrapper = new ZopflipngBin();
        $this->binPath = $binWrapper->getBinPath();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            '8bit' => null,
            'transparent' => null,
            'iterations' => null,
            'iterationsLarge' => null,
            'more' => null
        ]);

        $resolver->setAllowedTypes('8bit', ['null', 'bool']);
        $resolver->setAllowedTypes('transparent', ['null', 'bool']);
        $resolver->setAllowedTypes('iterations', ['null', 'int']);
        $resolver->setAllowedTypes('iterationsLarge', ['null', 'int']);
        $resolver->setAllowedTypes('more', ['null', 'bool']);
    }

    public function optimize($input)
    {
        if (!is_resource($input)) {
            throw new \Exception('Expected a resource type');
        }

        if (!$this->fs->isPng($input)) {
            return $input;
        }

        $args = ['-y'];

        $options = $this->options;

        if ($options['8bit']) {
            array_push($args, '--lossy_8bit');
        }

        if ($options['transparent']) {
            array_push($args, '--lossy_transparent');
        }

        if ($options['iterations'] || is_numeric($options['iterations'])) {
            array_push($args, '--iterations=' . $options['iterations']);
        }

        if ($options['iterationsLarge'] || is_numeric($options['iterationsLarge'])) {
            array_push($args, '--iterationsLarge=' . $options['iterationsLarge']);
        }

        if ($options['more']) {
            array_push($args, '-m');
        }

        array_push($args, '${input}');
        array_push($args, '${output}');

        return $this->execute($input, $this->binPath, $args);
    }
}

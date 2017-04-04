<?php
namespace Itgalaxy\Imagemin\Optimizer;

use Itgalaxy\Imagemin\Bin\AdvpngBin;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvpngOptimizer extends OptimizerAbstract implements OptimizerInterface
{
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $binWrapper = new AdvpngBin();
        $this->binPath = $binWrapper->getBinPath();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'optimizationLevel' => 3
        ]);
        $resolver->setAllowedTypes('optimizationLevel', ['int']);
        $resolver->setAllowedValues('optimizationLevel', function ($value) {
            return $value >= 0 && $value <= 4;
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

        $args = ['--recompress', '-q'];
        $options = $this->options;

        if (is_numeric($options['optimizationLevel'])) {
            array_push($args, '-' . $options['optimizationLevel']);
        }

        array_push($args, '${input}');

        // Todo use ProcessBuilder, not param
        return $this->execute($input, $this->binPath, $args, true);
    }
}

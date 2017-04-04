<?php
namespace Itgalaxy\Imagemin\Optimizer;

use Itgalaxy\Imagemin\Bin\GuetzliBin;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GuetzliOptimizer extends OptimizerAbstract implements OptimizerInterface
{
    protected $binPath = null;

    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $binWrapper = new GuetzliBin();
        $this->binPath = $binWrapper->getBinPath();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'quality' => null
        ]);
        $resolver->setAllowedTypes('quality', ['null', 'int']);
        $resolver->setAllowedValues('quality', function ($value) {
            return $value >= 0 && $value <= 100;
        });
    }

    public function optimize($input)
    {
        if (!is_resource($input)) {
            throw new \Exception('Expected a resource type');
        }

        if (!$this->fs->isPng($input) && !$this->fs->isJpg($input)) {
            return $input;
        }

        $options = $this->options;

        $args = [];

        if ($options['quality'] || is_numeric($options['quality'])) {
            array_push($args, '--quality');
            array_push($args, $options['quality']);
        }

        array_push($args, '${input}');
        array_push($args, '${output}');

        return $this->execute($input, $this->binPath, $args);
    }
}

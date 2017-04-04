<?php
namespace Itgalaxy\Imagemin\Optimizer;

use Itgalaxy\Imagemin\Bin\PngcrushBin;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PngcrushOptimizer extends OptimizerAbstract implements OptimizerInterface
{
    protected $binPath = null;

    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $binWrapper = new PngcrushBin();
        $this->binPath = $binWrapper->getBinPath();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'reduce' => null
        ]);

        $resolver->setAllowedTypes('reduce', ['null', 'bool']);
    }

    public function optimize($input)
    {
        if (!is_resource($input)) {
            throw new \Exception('Expected a resource type');
        }

        if (!$this->fs->isPng($input)) {
            return $input;
        }

        $args = [
            '-brute',
            '-force',
            '-q'
        ];

        $options = $this->options;

        if ($options['reduce']) {
            array_push($args, '-reduce');
        } else {
            array_push($args, '-noreduce');
        }

        array_push($args, '${input}');
        array_push($args, '${output}');

        return $this->execute($input, $this->binPath, $args);
    }
}

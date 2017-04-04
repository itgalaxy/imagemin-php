<?php
namespace Itgalaxy\Imagemin\Optimizer;

use Itgalaxy\Imagemin\Bin\PngquantBin;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PngquantOptimizer extends OptimizerAbstract implements OptimizerInterface
{
    protected $binPath = null;

    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $binWrapper = new PngquantBin();
        $this->binPath = $binWrapper->getBinPath();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'floyd' => null,
            'nofs' => null,
            'posterize' => null,
            'quality' => null,
            'speed' => null,
            'verbose' => null
        ]);

        $resolver->setAllowedTypes('floyd', ['null', 'int', 'float', 'bool']);
        $resolver->setAllowedValues('floyd', function ($value) {
            return  ($value === null)
                || (is_numeric($value) && $value >= 0 && $value <= 1)
                || (is_bool($value));
        });
        $resolver->setAllowedTypes('nofs', ['null', 'bool']);
        $resolver->setAllowedTypes('posterize', ['null', 'int']);
        $resolver->setAllowedValues('posterize', function ($value) {
            return  ($value === null) || (is_numeric($value) && intval($value) >= 0 && intval($value <= 4));
        });
        $resolver->setAllowedTypes('quality', ['null', 'string']);
        $resolver->setAllowedValues('quality', function ($value) {
            return  ($value === null) || (is_string($value) && intval($value) >= 0 && intval($value <= 100));
        });
        $resolver->setAllowedTypes('speed', ['null', 'int']);
        $resolver->setAllowedValues('speed', function ($value) {
            return  ($value === null) || (is_numeric($value) && $value >= 1 && $value <= 10);
        });
        $resolver->setAllowedTypes('verbose', ['null', 'bool']);
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

        array_push($args, '--force');
        array_push($args, '--output');
        array_push($args, '${output}');
        array_push($args, '${input}');

        $options = $this->options;

        if ($options['floyd'] || is_numeric($options['floyd']) || is_bool($options['floyd'])) {
            if (is_numeric($options['floyd'])) {
                array_push($args, '--floyd=' . $options['floyd']);
            } else if (is_bool($options['floyd']) && $options['floyd'] === true) {
                array_push($args, '--floyd');
            }
        }

        if ($options['nofs']) {
            array_push($args, '--nofs');
        }

        if ($options['posterize']) {
            array_push($args, '--posterize');
            array_push($args, $options['posterize']);
        }

        if ($options['quality'] || (intval($options['quality']) >= 0 && is_numeric(intval($options['quality'])))) {
            array_push($args, '--quality');
            array_push($args, intval($options['quality']));
        }

        if ($options['speed'] || is_numeric($options['speed'])) {
            array_push($args, '--speed');
            array_push($args, $options['speed']);
        }

        if ($options['verbose']) {
            array_push($args, '--verbose');
        }

        // Todo ignore 99 error code
        return $this->execute($input, $this->binPath, $args);
    }
}

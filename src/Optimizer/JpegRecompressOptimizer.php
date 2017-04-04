<?php
namespace Itgalaxy\Imagemin\Optimizer;

use Itgalaxy\Imagemin\Bin\JpegRecompressBin;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JpegRecompressOptimizer extends OptimizerAbstract implements OptimizerInterface
{
    protected $binPath = null;

    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $binWrapper = new JpegRecompressBin();
        $this->binPath = $binWrapper->getBinPath();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'accurate' => null,
            'quality' => null,
            'method' => null,
            'target' => null,
            'min' => null,
            'max' => null,
            'loops' => null,
            'defish' => null,
            'zoom' => null,
            'progressive' => null,
            'subsample' => null,
            'strip' => null
        ]);
        $resolver->setAllowedTypes('accurate', ['null', 'bool']);
        $resolver->setAllowedTypes('quality', ['null', 'string']);
        $resolver->setAllowedValues('quality', function ($value) {
            return  ($value === null)
                || (is_string($value) && in_array($value, ['low', 'medium', 'high', 'veryhigh']));
        });
        $resolver->setAllowedTypes('method', ['null', 'string']);
        $resolver->setAllowedValues('method', function ($value) {
            return  ($value === null)
                || (is_string($value) && in_array($value, ['mpe', 'ssim', 'ms-ssim', 'smallfry']));
        });
        $resolver->setAllowedTypes('target', ['null', 'int', 'float']);
        $resolver->setAllowedTypes('min', ['null', 'int']);
        $resolver->setAllowedTypes('max', ['null', 'int']);
        $resolver->setAllowedTypes('loops', ['null', 'int']);
        $resolver->setAllowedTypes('defish', ['null', 'int']);
        $resolver->setAllowedTypes('progressive', ['null', 'bool']);
        $resolver->setAllowedTypes('subsample', ['null', 'string']);
        $resolver->setAllowedValues('subsample', function ($value) {
            return  ($value === null) || (is_string($value) && in_array($value, ['default', 'disable']));
        });
        $resolver->setAllowedTypes('strip', ['null', 'bool']);
    }

    public function optimize($input)
    {
        if (!is_resource($input)) {
            throw new \Exception('Expected a resource type');
        }

        if (!$this->fs->isJpg($input)) {
            return $input;
        }

        $args = [
            '--quiet'
        ];

        $options = $this->options;

        if ($options['accurate']) {
            array_push($args, '--accurate');
        }

        if ($options['quality'] || is_string($options['quality'])) {
            array_push($args, '--quality');
            array_push($args, $options['quality']);
        }

        if ($options['method'] || is_string($options['method'])) {
            array_push($args, '--method');
            array_push($args, $options['method']);
        }

        if ($options['target'] || is_numeric($options['target'])) {
            array_push($args, '--target');
            array_push($args, $options['target']);
        }

        if ($options['min'] || is_numeric($options['min'])) {
            array_push($args, '--min');
            array_push($args, $options['min']);
        }

        if ($options['max'] || is_numeric($options['max'])) {
            array_push($args, '--max');
            array_push($args, $options['max']);
        }

        if ($options['loops'] || is_numeric($options['loops'])) {
            array_push($args, '--loops');
            array_push($args, $options['loops']);
        }

        if ($options['defish'] || is_numeric($options['defish'])) {
            array_push($args, '--defish');
            array_push($args, $options['defish']);
        }

        if ($options['zoom'] || is_numeric($options['zoom'])) {
            array_push($args, '--zoom');
            array_push($args, $options['zoom']);
        }

        if ($options['progressive'] || (is_bool($options['progressive']) && $options['progressive'] === false)) {
            array_push($args, '--no-progressive');
        }

        if ($options['subsample'] || is_string($options['subsample'])) {
            array_push($args, '--subsample');
            array_push($args, $options['subsample']);
        }

        if (!$options['strip'] || (is_bool($options['strip']) && $options['strip'] !== false)) {
            array_push($args, '--strip');
        }

        array_push($args, '${input}');
        array_push($args, '${output}');

        return $this->execute($input, $this->binPath, $args);
    }
}

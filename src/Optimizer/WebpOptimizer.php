<?php
namespace Itgalaxy\Imagemin\Optimizer;

use Itgalaxy\Imagemin\Bin\CwebpBin;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WebpOptimizer extends OptimizerAbstract implements OptimizerInterface
{
    protected $binPath = null;

    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $binWrapper = new CwebpBin();
        $this->binPath = $binWrapper->getBinPath();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'preset' => null,
            'quality' => null,
            'alphaQuality' => null,
            'method' => null,
            'size' => null,
            'sns' => null,
            'filter' => null,
            'autoFilter' => null,
            'sharpness' => null,
            'lossless' => null
        ]);

        $resolver->setAllowedTypes('preset', ['null', 'string']);
        $resolver->setAllowedValues('preset', function ($value) {
            return ($value === null)
                || (is_string($value) && in_array($value, [
                    'default',
                    'photo',
                    'picture',
                    'drawing',
                    'icon',
                    'text'
                ]));
        });
        $resolver->setAllowedTypes('quality', ['null', 'integer']);
        $resolver->setAllowedValues('quality', function ($value) {
            return ($value === null) || ($value >= 0 && $value <= 100);
        });
        $resolver->setAllowedTypes('alphaQuality', ['null', 'integer']);
        $resolver->setAllowedValues('alphaQuality', function ($value) {
            return ($value === null) || ($value >= 0 && $value <= 100);
        });
        $resolver->setAllowedTypes('method', ['null', 'integer']);
        $resolver->setAllowedValues('method', function ($value) {
            return ($value === null) || ($value >= 0 && $value <= 6);
        });
        $resolver->setAllowedTypes('size', ['null', 'integer']);
        $resolver->setAllowedTypes('sns', ['null', 'integer']);
        $resolver->setAllowedValues('sns', function ($value) {
            return ($value === null) || ($value >= 0 && $value <= 100);
        });
        $resolver->setAllowedTypes('filter', ['null', 'integer']);
        $resolver->setAllowedValues('filter', function ($value) {
            return ($value === null) || ($value >= 0 && $value <= 100);
        });
        $resolver->setAllowedTypes('autoFilter', ['null', 'boolean']);
        $resolver->setAllowedTypes('sharpness', ['null', 'integer']);
        $resolver->setAllowedValues('sharpness', function ($value) {
            return ($value === null) || ($value >= 0 && $value <= 7);
        });
        $resolver->setAllowedTypes('lossless', ['null', 'boolean']);
    }

    public function optimize($input)
    {
        if (!is_resource($input)) {
            throw new \Exception('Expected a resource type');
        }

        if (!$this->fs->isPNG($input)
            && !$this->fs->isJPG($input)
            && !$this->fs->isTIF($input)
            && !$this->fs->isWebP($input)
        ) {
            return $input;
        }

        $args = [
            '-quiet',
            '-mt'
        ];

        $options = $this->options;

        if ($options['preset']) {
            array_push($args, '-preset');
            array_push($args, $options['preset']);
        }

        if ($options['quality'] || is_numeric($options['quality'])) {
            array_push($args, '-q');
            array_push($args, $options['quality']);
        }

        if ($options['alphaQuality'] || is_numeric($options['alphaQuality'])) {
            array_push($args, '-alpha_q');
            array_push($args, $options['alphaQuality']);
        }

        if ($options['method'] || is_numeric($options['method'])) {
            array_push($args, '-m');
            array_push($args, $options['method']);
        }

        if ($options['size'] || is_numeric($options['size'])) {
            array_push($args, '-size');
            array_push($args, $options['size']);
        }

        if ($options['sns'] || is_numeric($options['sns'])) {
            array_push($args, '-sns');
            array_push($args, $options['sns']);
        }

        if ($options['filter'] || is_numeric($options['filter'])) {
            array_push($args, '-f');
            array_push($args, $options['filter']);
        }

        if ($options['autoFilter']) {
            array_push($args, '-af');
            array_push($args, $options['autoFilter']);
        }

        if ($options['sharpness'] || is_numeric($options['sharpness'])) {
            array_push($args, '-sharpness');
            array_push($args, $options['sharpness']);
        }

        if ($options['lossless']) {
            array_push($args, '-lossless');
        }

        array_push($args, '-o');
        array_push($args, '${output}');
        array_push($args, '${input}');

        return $this->execute($input, $this->binPath, $args);
    }
}

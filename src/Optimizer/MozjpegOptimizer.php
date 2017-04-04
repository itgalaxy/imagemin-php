<?php
namespace Itgalaxy\Imagemin\Optimizer;

use Itgalaxy\Imagemin\Bin\MozjpegBin;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MozjpegOptimizer extends OptimizerAbstract implements OptimizerInterface
{
    protected $binPath = null;

    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $binWrapper = new MozjpegBin();
        $this->binPath = $binWrapper->getBinPath();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'quality' => null,
            'optimize' => true,
            'progressive' => true,
            'targa' => false,
            'revert' => false,
            'fastcrush' => false,
            'dcScanOpt' => null,
            'notrellis' => false,
            'notrellisDC' => null,
            'tune' => null,
            'noovershoot' => false,
            'arithmetic' => false,
            'dct' => null,
            'quantTable' => null,
            'smooth' => null,
            'maxmemory' => null
        ]);

        $resolver->setAllowedTypes('quality', ['null', 'int']);
        $resolver->setAllowedValues('quantTable', function ($value) {
            return ($value === null) || ($value >= 0 && $value <= 100);
        });
        $resolver->setAllowedTypes('optimize', ['null', 'bool']);
        $resolver->setAllowedTypes('progressive', ['null', 'bool']);
        $resolver->setAllowedTypes('targa', ['null', 'bool']);
        $resolver->setAllowedTypes('revert', ['null', 'bool']);
        $resolver->setAllowedTypes('fastcrush', ['null', 'bool']);
        $resolver->setAllowedTypes('dcScanOpt', ['null', 'int']);
        $resolver->setAllowedValues('dcScanOpt', function ($value) {
            return ($value === null) || ($value >= 0 && $value <= 2);
        });
        $resolver->setAllowedTypes('notrellis', ['null', 'bool']);
        $resolver->setAllowedTypes('notrellisDC', ['null', 'bool']);
        $resolver->setAllowedTypes('tune', ['null', 'string']);
        $resolver->setAllowedValues('tune', [null, 'psnr', 'hvs-psnr', 'ssim', 'ms-ssim']);
        $resolver->setAllowedTypes('noovershoot', ['null', 'bool']);
        $resolver->setAllowedTypes('arithmetic', ['null', 'bool']);
        $resolver->setAllowedTypes('dct', ['null', 'string']);
        $resolver->setAllowedValues('dct', [null, 'int', 'fast', 'float']);
        $resolver->setAllowedTypes('quantTable', ['null', 'int']);
        $resolver->setAllowedValues('quantTable', function ($value) {
            return ($value === null) || ($value >= 0 && $value <= 5);
        });
        $resolver->setAllowedTypes('smooth', ['null', 'int']);
        $resolver->setAllowedValues('smooth', function ($value) {
            return ($value === null) || ($value >= 0 && $value <= 100);
        });
        $resolver->setAllowedTypes('maxmemory', ['null', 'int']);
    }

    public function optimize($input)
    {
        if (!is_resource($input)) {
            throw new \Exception('Expected a resource type');
        }

        if (!$this->fs->isJpg($input)) {
            return $input;
        }

        $options = $this->options;
        $args = ['-outfile', '${output}'];

        if ($options['quality'] || $options['quality'] === 0) {
            array_push($args, '-quality');
            array_push($args, $options['quality']);
        }

        if ($options['progressive'] === false) {
            array_push($args, '-baseline');
        }

        if ($options['targa']) {
            array_push($args, '-targa');
        }

        if ($options['revert']) {
            array_push($args, '-revert');
        }

        if ($options['fastcrush']) {
            array_push($args, '-fastcrush');
        }

        if ($options['dcScanOpt'] || $options['dcScanOpt'] === 0) {
            array_push($args, '-dc-scan-opt');
            array_push($args, $options['dcScanOpt']);
        }

        if ($options['notrellis']) {
            array_push($args, '-notrellis');
        }

        if ($options['notrellisDC']) {
            array_push($args, '-notrellis-dc');
        }

        if ($options['tune']) {
            array_push($args, '-tune-' . $options['tune']);
        }

        if ($options['noovershoot']) {
            array_push($args, '-noovershoot');
        }

        if ($options['arithmetic']) {
            array_push($args, '-arithmetic');
        }

        if ($options['dct']) {
            array_push($args, '-dct');
            array_push($args, $options['dct']);
        }

        if ($options['quantTable'] || is_numeric($options['quantTable'])) {
            array_push($args, '-quant-table');
            array_push($args, $options['quantTable']);
        }

        if ($options['smooth'] || is_numeric($options['smooth'])) {
            array_push($args, '-smooth');
            array_push($args, $options['smooth']);
        }

        if ($options['maxmemory'] || is_numeric($options['maxmemory'])) {
            array_push($args, '-maxmemory');
            array_push($args, $options['maxmemory']);
        }

        array_push($args, '${input}');

        return $this->execute($input, $this->binPath, $args);
    }
}

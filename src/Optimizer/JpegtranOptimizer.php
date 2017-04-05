<?php
namespace Itgalaxy\Imagemin\Optimizer;

use Itgalaxy\Imagemin\Bin\JpegtranBin;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JpegtranOptimizer extends OptimizerAbstract implements OptimizerInterface
{
    protected $binPath = null;

    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $binWrapper = new JpegtranBin();
        $this->binPath = $binWrapper->getBinPath();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'progressive' => null,
            'arithmetic' => null
        ]);
        $resolver->setAllowedTypes('progressive', ['null', 'bool']);
        $resolver->setAllowedTypes('arithmetic', ['null', 'bool']);
    }

    public function optimize($input)
    {
        if (!is_resource($input)) {
            throw new \Exception('Expected a resource type');
        }

        if (!$this->fs->isJPG($input)) {
            return $input;
        }

        $options = $this->options;

        $args = ['-copy', 'none'];

        if ($options['progressive']) {
            array_push($args, '-progressive');
        }

        if ($options['arithmetic']) {
            array_push($args, '-arithmetic');
        } else {
            array_push($args, '-optimize');
        }

        array_push($args, '-outfile');
        array_push($args, '${output}');
        array_push($args, '${input}');

        return $this->execute($input, $this->binPath, $args);
    }
}

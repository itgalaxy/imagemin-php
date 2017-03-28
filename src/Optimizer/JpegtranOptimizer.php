<?php
namespace Itgalaxy\Imagemin\Optimizer;

use Itgalaxy\Imagemin\Bin\JpegtranBin;

class JpegtranOptimizer extends OptimizerAbstract implements OptimizerInterface
{
    protected $binWrapper = null;

    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $this->binWrapper = new JpegtranBin();
    }

    public function optimize($input)
    {
        if (!$this->fs->isJpg($input)) {
            return $input;
        }

        $args = ['-copy', 'none'];

        if (isset($options['progressive'])) {
            array_push($args, '-progressive');
        }

        if (isset($options['arithmetic'])) {
            array_push($args, '-arithmetic');
        } else {
            array_push($args, '-optimize');
        }

        array_push($args, '-outfile');
        array_push($args, '${output}');
        array_push($args, '${input}');

        $binPath = $this->binWrapper->getBinPath();

        return $this->execute($input, $binPath, $args);
    }
}

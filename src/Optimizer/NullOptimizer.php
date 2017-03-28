<?php
namespace Itgalaxy\Imagemin\Optimizer;

class NullOptimizer extends OptimizerAbstract implements OptimizerInterface
{
    protected $options = [
        'name' => 'null',
    ];

    // Todo need test
    public function optimize($input)
    {
        return $input;
    }
}

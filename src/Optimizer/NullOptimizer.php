<?php
namespace Itgalaxy\Imagemin\Optimizer;

class NullOptimizer extends OptimizerAbstract implements OptimizerInterface
{
    public function optimize($input)
    {
        if (!is_resource($input)) {
            throw new \Exception('Expected a resource type');
        }

        return $input;
    }
}

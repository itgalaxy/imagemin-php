<?php
namespace Itgalaxy\Imagemin\Optimizer;

class ChainOptimizer implements OptimizerInterface
{
    private $optimizers = [];

    public function __construct(array $optimizers = [])
    {
        $this->optimizers = $optimizers;
    }

    public function optimize($input)
    {
        $result = $input;

        foreach ($this->optimizers as $optimizer) {
            $result = $optimizer->optimize($result);
        }

        return $result;
    }
}

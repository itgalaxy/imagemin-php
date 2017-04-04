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
        if (!is_resource($input)) {
            throw new \Exception('Expected a resource type');
        }

        if (count($this->optimizers) == 0) {
            throw new \Exception('Chain optimizer should contain at least one optimizer');
        }

        $result = $input;

        foreach ($this->optimizers as $optimizer) {
            $result = $optimizer->optimize($result);
        }

        return $result;
    }
}

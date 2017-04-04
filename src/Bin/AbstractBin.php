<?php
namespace Itgalaxy\Imagemin\Bin;

use Symfony\Component\Process\ExecutableFinder;

abstract class AbstractBin
{
    protected $name = null;

    protected $binDir = __DIR__ . '/../../bin';

    public function __construct()
    {
        $function = new \ReflectionClass($this);
        $className = $function->getShortName();

        $this->name = strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', str_replace('Bin', '', $className)));
    }

    abstract protected function getBinWrapper();

    public function install($args = [])
    {
        $this->getBinWrapper()->run($args);
    }

    public function recompile($args = [])
    {
        // Todo need implement
    }

    public function getBinPath()
    {
        return $this->getBinWrapper()->path();
    }

    // Todo add pre testing
    // Todo add option
    private function findExecutable($name)
    {
        $executableFinder = new ExecutableFinder();

        return $this->option($name . '_bin', function () use ($name, $executableFinder) {
            return $executableFinder->find($name, $name);
        });
    }
}

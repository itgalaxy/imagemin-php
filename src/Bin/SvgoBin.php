<?php
namespace Itgalaxy\Imagemin\Bin;

use Itgalaxy\BinWrapper\BinWrapper;

class SvgoBin extends AbstractBin
{
    protected $url = 'https://raw.github.com/imagemin/pngout-bin/v3.0.0/vendor/';

    protected function getBinWrapper()
    {
        $url = $this->url;
        $binWrapper = new BinWrapper();

        return $binWrapper
            ->src($url . 'bin')
            ->dest($this->binDir . '/' . $this->name)
            ->using('svgo');
    }

    public function getBinPath()
    {
        return $this->getBinWrapper()->path();
    }
}

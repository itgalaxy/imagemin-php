<?php
namespace Itgalaxy\Imagemin\Bin;

use Itgalaxy\BinWrapper\BinWrapper;

class AdvpngBin extends AbstractBin
{
    // Todo change master on version after first initial
    protected $url = 'https://raw.github.com/itgalaxy/imagemin-php/master/bin/advpng/';

    protected function getBinWrapper()
    {
        $url = $this->url;
        $platform = strtolower(PHP_OS);
        $binWrapper = new BinWrapper();

        return $binWrapper
            ->src($url . 'macos/advpng', 'darwin')
            ->src($url . 'linux/advpng', 'linux')
            ->src($url . 'win32/advpng.exe', 'windows')
            ->dest($this->binDir . '/' . $this->name)
            ->using(substr($platform, 0, 3) == 'win' ? 'advpng.exe' : 'advpng');
    }
}

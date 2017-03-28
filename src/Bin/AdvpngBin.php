<?php
namespace Itgalaxy\Imagemin\Bin;

use Itgalaxy\BinWrapper\BinWrapper;

class AdvpngBin extends AbstractBin
{
    protected $url = 'https://raw.github.com/imagemin/advpng-bin/v3.0.0/vendor/';

    protected function getBinWrapper()
    {
        $url = $this->url;
        $platform = strtolower(PHP_OS);
        $binWrapper = new BinWrapper();

        return $binWrapper
            ->src($url . 'osx/advpng', 'darwin')
            ->src($url . 'linux/advpng', 'linux')
            ->src($url . 'win32/advpng.exe', 'win32')
            ->dest($this->binDir . '/' . $this->name)
            ->using(substr($platform, 0, 3) === 'win' ? 'advpng.exe' : 'advpng');
    }
}

<?php
namespace Itgalaxy\Imagemin\Bin;

use Itgalaxy\BinWrapper\BinWrapper;

class PngquantBin extends AbstractBin
{
    protected $url = 'https://raw.github.com/imagemin/pngquant-bin/v3.1.1/vendor/';

    protected function getBinWrapper()
    {
        $url = $this->url;
        $platform = strtolower(PHP_OS);
        $binWrapper = new BinWrapper();

        return $binWrapper
            ->src($url . 'macos/pngquant', 'darwin')
            ->src($url . 'linux/x86/pngquant', 'linux', 'x86')
            ->src($url . 'linux/x64/pngquant', 'linux', 'x64')
            ->src($url . 'freebsd/x64/pngquant', 'freebsd', 'x64')
            ->src($url . 'win/pngquant.exe', 'windows')
            ->dest($this->binDir . '/' . $this->name)
            ->using(substr($platform, 0, 3) === 'win' ? 'pngquant.exe' : 'pngquant');
    }
}

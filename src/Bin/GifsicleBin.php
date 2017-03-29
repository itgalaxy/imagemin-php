<?php
namespace Itgalaxy\Imagemin\Bin;

use Itgalaxy\BinWrapper\BinWrapper;

class GifsicleBin extends AbstractBin
{
    protected $url = 'https://raw.githubusercontent.com/imagemin/gifsicle-bin/v3.0.4/vendor/';

    protected function getBinWrapper()
    {
        $url = $this->url;
        $platform = strtolower(PHP_OS);
        $binWrapper = new BinWrapper();

        return $binWrapper
            ->src($url . 'macos/gifsicle', 'darwin')
            ->src($url . 'linux/x86/gifsicle', 'linux', 'x86')
            ->src($url . 'linux/x64/gifsicle', 'linux', 'x64')
            ->src($url . 'freebsd/x86/gifsicle', 'freebsd', 'x86')
            ->src($url . 'freebsd/x64/gifsicle', 'freebsd', 'x64')
            ->src($url . 'win/x86/gifsicle.exe', 'windows', 'x86')
            ->src($url . 'win/x64/gifsicle.exe', 'windows', 'x64')
            ->dest($this->binDir . '/' . $this->name)
            ->using(substr($platform, 0, 3) === 'win' ? 'gifsicle.exe' : 'gifsicle');
    }
}

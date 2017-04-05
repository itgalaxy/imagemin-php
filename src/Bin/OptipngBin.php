<?php
namespace Itgalaxy\Imagemin\Bin;

use Itgalaxy\BinWrapper\BinWrapper;

class OptipngBin extends AbstractBin
{
    protected $url = 'https://raw.github.com/itgalaxy/imagemin-php/master/bin/optipng/';

    protected function getBinWrapper()
    {
        $url = $this->url;
        $platform = strtolower(PHP_OS);
        $binWrapper = new BinWrapper();

        return $binWrapper
            ->src($url . 'macos/optipng', 'darwin')
            ->src($url . 'linux/x86/optipng', 'linux', 'x86')
            ->src($url . 'linux/x64/optipng', 'linux', 'x64')
            ->src($url . 'freebsd/x86/optipng', 'freebsd', 'x86')
            ->src($url . 'freebsd/x64/optipng', 'freebsd', 'x64')
            ->src($url . 'sunos/x86/optipng', 'sunos', 'x86')
            ->src($url . 'sunos/x64/optipng', 'sunos', 'x64')
            ->src($url . 'win/optipng.exe', 'windows')
            ->dest($this->binDir . '/' . $this->name)
            ->using(substr($platform, 0, 3) == 'win' ? 'optipng.exe' : 'optipng');
    }
}

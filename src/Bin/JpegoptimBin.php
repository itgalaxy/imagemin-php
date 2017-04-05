<?php
namespace Itgalaxy\Imagemin\Bin;

use Itgalaxy\BinWrapper\BinWrapper;

class JpegoptimBin extends AbstractBin
{
    protected $url = 'https://raw.github.com/itgalaxy/imagemin-php/master/bin/jpegoptim/';

    protected function getBinWrapper()
    {
        $url = $this->url;
        $platform = strtolower(PHP_OS);
        $binWrapper = new BinWrapper();

        return $binWrapper
            ->src($url . 'macos/jpegoptim', 'darwin')
            ->src($url . 'linux/jpegoptim', 'linux')
            ->src($url . 'win32/jpegoptim.exe', 'windows')
            ->dest($this->binDir . '/' . $this->name)
            ->using(substr($platform, 0, 3) == 'win' ? 'jpegoptim.exe' : 'jpegoptim');
    }
}

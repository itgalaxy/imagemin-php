<?php
namespace Itgalaxy\Imagemin\Bin;

use Itgalaxy\BinWrapper\BinWrapper;

class JpegoptimBin extends AbstractBin
{
    protected $url = 'https://raw.githubusercontent.com/imagemin/jpegoptim-bin/v3.0.0/vendor/';

    protected function getBinWrapper()
    {
        $url = $this->url;
        $platform = strtolower(PHP_OS);
        $binWrapper = new BinWrapper();

        return $binWrapper
            ->src($url . 'osx/jpegoptim', 'darwin')
            ->src($url . 'linux/jpegoptim', 'linux')
            ->src($url . 'win32/jpegoptim.exe', 'win32')
            ->dest($this->binDir . '/' . $this->name)
            ->using(substr($platform, 0, 3) === 'win' ? 'jpegoptim.exe' : 'jpegoptim');
    }
}
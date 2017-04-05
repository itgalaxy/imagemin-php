<?php
namespace Itgalaxy\Imagemin\Bin;

use Itgalaxy\BinWrapper\BinWrapper;

class MozjpegBin extends AbstractBin
{
    protected $url = 'https://raw.github.com/itgalaxy/imagemin-php/master/bin/mozjpeg/';

    protected function getBinWrapper()
    {
        $url = $this->url;
        $platform = strtolower(PHP_OS);
        $binWrapper = new BinWrapper();

        return $binWrapper
            ->src($url . 'macos/cjpeg', 'darwin')
            ->src($url . 'linux/cjpeg', 'linux')
            ->src($url . 'win/cjpeg.exe', 'windows')
            ->dest($this->binDir . '/' . $this->name)
            ->using(substr($platform, 0, 3) == 'win' ? 'cjpeg.exe' : 'cjpeg');
    }

    public function install($args = [])
    {
        parent::install(['-version']);
    }
}

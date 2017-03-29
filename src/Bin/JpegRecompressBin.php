<?php
namespace Itgalaxy\Imagemin\Bin;

use Itgalaxy\BinWrapper\BinWrapper;

class JpegRecompressBin extends AbstractBin
{
    protected $url = 'https://raw.github.com/imagemin/jpeg-recompress-bin/v3.0.1/vendor/';

    protected function getBinWrapper()
    {
        $url = $this->url;
        $platform = strtolower(PHP_OS);
        $binWrapper = new BinWrapper();

        return $binWrapper
            ->src($url . 'osx/jpeg-recompress', 'darwin')
            ->src($url . 'linux/jpeg-recompress', 'linux')
            ->src($url . 'win/jpeg-recompress.exe', 'windows')
            ->dest($this->binDir . '/' . $this->name)
            ->using(substr($platform, 0, 3) === 'win' ? 'jpeg-recompress.exe' : 'jpeg-recompress');
    }
}

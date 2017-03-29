<?php
namespace Itgalaxy\Imagemin\Bin;

use Itgalaxy\BinWrapper\BinWrapper;

class PngcrushBin extends AbstractBin
{
    // Todo bug with 404
    protected $url = 'https://raw.githubusercontent.com/imagemin/pngcrush-bin/v3.1.0/vendor/';

    protected function getBinWrapper()
    {
        $url = $this->url;
        $platform = strtolower(PHP_OS);
        $binWrapper = new BinWrapper();

        return $binWrapper
            ->src($url . 'osx/pngcrush', 'darwin')
            ->src($url . 'linux/pngcrush', 'linux')
            ->src($url . 'win/x64/pngcrush.exe', 'windows', 'x64')
            ->src($url . 'win/x86/pngcrush.exe', 'windows', 'x86')
            ->dest($this->binDir . '/' . $this->name)
            ->using(substr($platform, 0, 3) === 'win' ? 'pngcrush.exe' : 'pngcrush');
    }

    public function install($args = [])
    {
        parent::install(['-version']);
    }
}

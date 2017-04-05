<?php
namespace Itgalaxy\Imagemin\Bin;

use Itgalaxy\BinWrapper\BinWrapper;

class CwebpBin extends AbstractBin
{
    protected $url = 'https://raw.github.com/itgalaxy/imagemin-php/master/bin/cwebp/';

    protected function getBinWrapper()
    {
        $url = $this->url;
        $platform = strtolower(PHP_OS);
        $binWrapper = new BinWrapper();

        return $binWrapper
            ->src($url . 'macos/cwebp', 'darwin')
            ->src($url . 'linux/x86/cwebp', 'linux', 'x86')
            ->src($url . 'linux/x64/cwebp', 'linux', 'x64')
            ->src($url . 'win/x86/cwebp.exe', 'windows', 'x86')
            ->src($url . 'win/x64/cwebp.exe', 'windows', 'x64')
            ->dest($this->binDir . '/' . $this->name)
            ->using(substr($platform, 0, 3) == 'win' ? 'cwebp.exe' : 'cwebp');
    }

    public function install($args = [])
    {
        parent::install(!empty($args) ? $args : ['-version']);
    }
}

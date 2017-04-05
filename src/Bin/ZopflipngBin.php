<?php
namespace Itgalaxy\Imagemin\Bin;

use Itgalaxy\BinWrapper\BinWrapper;

class ZopflipngBin extends AbstractBin
{
    protected $url = 'https://raw.github.com/itgalaxy/imagemin-php/master/bin/zopflipng/';

    protected function getBinWrapper()
    {
        $url = $this->url;
        $platform = strtolower(PHP_OS);
        $binWrapper = new BinWrapper();

        return $binWrapper
            ->src($url . 'macos/zopflipng', 'darwin')
            ->src($url . 'linux/zopflipng', 'linux')
            ->src($url . 'win32/zopflipng.exe', 'windows')
            ->dest($this->binDir . '/' . $this->name)
            ->using(substr($platform, 0, 3) == 'win' ? 'zopflipng.exe' : 'zopflipng');
    }

    public function install($args = [])
    {
        parent::install(!empty($args) ? $args : ['--help']);
    }
}

<?php
namespace Itgalaxy\Imagemin\Bin;

use Itgalaxy\BinWrapper\BinWrapper;

class ZopflipngBin extends AbstractBin
{
    protected $url = 'https://raw.github.com/imagemin/zopflipng-bin/v4.0.0/vendor/';

    protected function getBinWrapper()
    {
        $url = $this->url;
        $platform = strtolower(PHP_OS);
        $binWrapper = new BinWrapper();

        return $binWrapper
            ->src($url . 'osx/zopflipng', 'darwin')
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

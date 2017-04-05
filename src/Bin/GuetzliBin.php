<?php
namespace Itgalaxy\Imagemin\Bin;

use Itgalaxy\BinWrapper\BinWrapper;

class GuetzliBin extends AbstractBin
{
    protected $url = 'https://raw.githubusercontent.com/imagemin/guetzli-bin/v0.1.0/vendor/';

    protected function getBinWrapper()
    {
        $url = $this->url;
        $platform = strtolower(PHP_OS);
        $binWrapper = new BinWrapper();

        return $binWrapper
            ->src($url . 'macos/guetzli', 'darwin')
            ->src($url . 'linux/guetzli', 'linux')
            ->src($url . 'win/guetzli.exe', 'windows')
            ->dest($this->binDir . '/' . $this->name)
            ->using(substr($platform, 0, 3) == 'win' ? 'guetzli.exe' : 'guetzli');
    }

    public function install($args = [])
    {
        parent::install(!empty($args) ? $args : ['-version']);
    }
}

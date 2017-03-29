<?php
namespace Itgalaxy\Imagemin\Bin;

use Itgalaxy\BinWrapper\BinWrapper;

class JpegtranBin extends AbstractBin
{
    protected $url = 'https://raw.githubusercontent.com/imagemin/jpegtran-bin/v3.2.0/vendor/';

    protected function getBinWrapper()
    {
        $url = $this->url;
        $platform = strtolower(PHP_OS);
        $binWrapper = new BinWrapper();

        return $binWrapper
            ->src($url . 'macos/jpegtran', 'darwin')
            ->src($url . 'linux/x86/jpegtran', 'linux', 'x86')
            ->src($url . 'linux/x64/jpegtran', 'linux', 'x64')
            ->src($url . 'freebsd/x86/jpegtran', 'freebsd', 'x86')
            ->src($url . 'freebsd/x64/jpegtran', 'freebsd', 'x64')
            ->src($url . 'sunos/x86/jpegtran', 'sunos', 'x86')
            ->src($url . 'sunos/x64/jpegtran', 'sunos', 'x64')
            ->src($url . 'win/x86/jpegtran.exe', 'windows', 'x86')
            ->src($url . 'win/x64/jpegtran.exe', 'windows', 'x64')
            ->src($url . 'win/x86/libjpeg-62.dll', 'windows', 'x86')
            ->src($url . 'win/x64/libjpeg-62.dll', 'windows', 'x64')
            ->dest($this->binDir . '/' . $this->name)
            ->using(substr($platform, 0, 3) === 'win' ? 'jpegtran.exe' : 'jpegtran');
    }

    public function install($args = [])
    {
        $fixtureTest = __DIR__ . '/pretest-fixtures/' . $this->name . '/test.jpg';
        $fixtureOptimizedTest = __DIR__ . '/pretest-fixtures/' . $this->name . '/test-optimized.jpg';

        parent::install(!empty($args)
            ? $args
            : ['-copy', 'none', '-optimize', '-outfile', $fixtureOptimizedTest, $fixtureTest]
        );
    }
}

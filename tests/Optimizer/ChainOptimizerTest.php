<?php
namespace Itgalaxy\Imagemin\Optimizer\Tests;

use Itgalaxy\Imagemin\Filesystem\Filesystem;
use Itgalaxy\Imagemin\Optimizer\ChainOptimizer;
use Itgalaxy\Imagemin\Optimizer\OptipngOptimizer;
use Itgalaxy\Imagemin\Optimizer\PngquantOptimizer;
use PHPUnit\Framework\TestCase;

class ChainOptimizerTest extends TestCase
{
    public function testOptimizeAPNG()
    {
        $src = FIXTURES_DIR . '/test.png';
        $stream = fopen($src, 'r');

        $chainOptimizer = new ChainOptimizer([
            new PngquantOptimizer(),
            new OptipngOptimizer()
        ]);
        $optimizedStream = $chainOptimizer->optimize($stream);

        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize > 0);
        $this->assertTrue($optimizerStreamSize < $sourceStreamSize);

        $fs = new Filesystem();
        $this->assertTrue($fs->isPng($optimizedStream));

        $fs->remove(stream_get_meta_data($optimizedStream)['uri']);
    }

    public function testThrowErrorWhenInputIsNotResource()
    {
        $this->expectException(\Exception::class);

        $chainOptimizer = new ChainOptimizer();
        $chainOptimizer->optimize('String');
    }

    public function testThrowErrorWhenNoOptimizers()
    {
        $this->expectException(\Exception::class);

        $src = FIXTURES_DIR . '/test.png';
        $stream = fopen($src, 'r');

        $chainOptimizer = new ChainOptimizer();
        $chainOptimizer->optimize($stream);
    }

    public function testSkipOptimizingANonPngFile()
    {
        $src = FIXTURES_DIR . '/test.jpg';
        $stream = fopen($src, 'r');

        $chainOptimizer = new ChainOptimizer([
            new PngquantOptimizer(),
            new OptipngOptimizer()
        ]);
        $optimizedStream = $chainOptimizer->optimize($stream);

        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize == $sourceStreamSize);
    }
}

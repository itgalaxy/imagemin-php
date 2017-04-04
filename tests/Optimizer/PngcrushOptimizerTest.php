<?php
namespace Itgalaxy\Imagemin\Optimizer\Tests;

use Itgalaxy\Imagemin\Filesystem\Filesystem;
use Itgalaxy\Imagemin\Optimizer\PngcrushOptimizer;
use PHPUnit\Framework\TestCase;

class PngcrushOptimizerTest extends TestCase
{
    /*public function testOptimizeAPNG()
    {
        $src = FIXTURES_DIR . '/test.png';
        $stream = fopen($src, 'r');

        $pngcrushOptimizer = new PngcrushOptimizer();
        $optimizedStream = $pngcrushOptimizer->optimize($stream);

        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize > 0);
        $this->assertTrue($optimizerStreamSize < $sourceStreamSize);

        $fs = new Filesystem();
        $this->assertTrue($fs->isPng($optimizedStream));

        $fs->remove(stream_get_meta_data($optimizedStream)['uri']);
    }*/

    public function testSupportOptipngOptions()
    {
        $src = FIXTURES_DIR . '/test.png';
        $stream = fopen($src, 'r');

        $pngcrushOptimizer = new PngcrushOptimizer([
            'reduce' => true
        ]);
        $optimizedStream = $pngcrushOptimizer->optimize($stream);
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

        $pngcrushOptimizer = new PngcrushOptimizer();
        $pngcrushOptimizer->optimize('String');
    }

    public function testSkipOptimizingANonPngFile()
    {
        $src = FIXTURES_DIR . '/test.jpg';
        $stream = fopen($src, 'r');

        $pngcrushOptimizer = new PngcrushOptimizer();
        $optimizedStream = $pngcrushOptimizer->optimize($stream);

        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize == $sourceStreamSize);
    }
}

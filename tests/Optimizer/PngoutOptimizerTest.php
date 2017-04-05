<?php
namespace Itgalaxy\Imagemin\Optimizer\Tests;

use Itgalaxy\Imagemin\Filesystem\Filesystem;
use Itgalaxy\Imagemin\Optimizer\PngoutOptimizer;
use PHPUnit\Framework\TestCase;

class PngoutOptimizerTest extends TestCase
{
    public function testOptimizeAPNG()
    {
        $src = FIXTURES_DIR . '/test.png';
        $stream = fopen($src, 'r');

        $optipngOptimizer = new PngoutOptimizer();
        $optimizedStream = $optipngOptimizer->optimize($stream);

        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize > 0);
        $this->assertTrue($optimizerStreamSize < $sourceStreamSize);

        $fs = new Filesystem();
        $this->assertTrue($fs->isPNG($optimizedStream));

        $fs->remove(stream_get_meta_data($optimizedStream)['uri']);
    }

    public function testSupportOptipngOptions()
    {
        $src = FIXTURES_DIR . '/test.png';
        $stream = fopen($src, 'r');

        $optipngOptimizer = new PngoutOptimizer([
            'strategy' => 1
        ]);
        $optimizedStream = $optipngOptimizer->optimize($stream);
        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize > 0);
        $this->assertTrue($optimizerStreamSize < $sourceStreamSize);

        $fs = new Filesystem();
        $this->assertTrue($fs->isPNG($optimizedStream));

        $fs->remove(stream_get_meta_data($optimizedStream)['uri']);
    }

    public function testThrowErrorWhenInputIsNotResource()
    {
        $this->expectException(\Exception::class);

        $optipngOptimizer = new PngoutOptimizer();
        $optipngOptimizer->optimize('String');
    }

    public function testSkipOptimizingANonPngFile()
    {
        $src = FIXTURES_DIR . '/test.jpg';
        $stream = fopen($src, 'r');

        $optipngOptimizer = new PngoutOptimizer();
        $optimizedStream = $optipngOptimizer->optimize($stream);

        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize == $sourceStreamSize);
    }
}

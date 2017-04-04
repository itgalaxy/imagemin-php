<?php
namespace Itgalaxy\Imagemin\Optimizer\Tests;

use Itgalaxy\Imagemin\Filesystem\Filesystem;
use Itgalaxy\Imagemin\Optimizer\GifsicleOptimizer;
use PHPUnit\Framework\TestCase;

class GifsicleOptimizerTest extends TestCase
{
    public function testOptimizeAGIF()
    {
        $src = FIXTURES_DIR . '/test.gif';
        $stream = fopen($src, 'r');

        $gifsicleOptimizer = new GifsicleOptimizer();
        $optimizedStream = $gifsicleOptimizer->optimize($stream);

        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize > 0);
        $this->assertTrue($optimizerStreamSize < $sourceStreamSize);

        $fs = new Filesystem();
        $this->assertTrue($fs->isGif($optimizedStream));

        $fs->remove(stream_get_meta_data($optimizedStream)['uri']);
    }

    public function testSupportGifsicleOptions()
    {
        $src = FIXTURES_DIR . '/test.gif';
        $stream = fopen($src, 'r');

        $gifsicleOptimizer = new GifsicleOptimizer([
            'optimizationLevel' => 3,
            'interlaced' => true,
            'colors' => 2
        ]);
        $optimizedStream = $gifsicleOptimizer->optimize($stream);
        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize > 0);
        $this->assertTrue($optimizerStreamSize < $sourceStreamSize);

        $fs = new Filesystem();
        $this->assertTrue($fs->isGif($optimizedStream));

        $fs->remove(stream_get_meta_data($optimizedStream)['uri']);
    }

    public function testThrowErrorWhenInputIsNotResource()
    {
        $this->expectException(\Exception::class);

        $gifsicleOptimizer = new GifsicleOptimizer();
        $gifsicleOptimizer->optimize('String');
    }

    public function testSkipOptimizingANonGIFFile()
    {
        $src = FIXTURES_DIR . '/test.png';
        $stream = fopen($src, 'r');

        $gifsicleOptimizer = new GifsicleOptimizer();
        $optimizedStream = $gifsicleOptimizer->optimize($stream);

        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize == $sourceStreamSize);
    }
}

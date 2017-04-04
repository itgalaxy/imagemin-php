<?php
namespace Itgalaxy\Imagemin\Optimizer\Tests;

use Itgalaxy\Imagemin\Filesystem\Filesystem;
use Itgalaxy\Imagemin\Optimizer\GuetzliOptimizer;
use PHPUnit\Framework\TestCase;

class GuetzliOptimizerTest extends TestCase
{
    public function testOptimizeAJPG()
    {
        $src = FIXTURES_DIR . '/test.jpg';
        $stream = fopen($src, 'r');

        $mozjpegOptimizer = new GuetzliOptimizer();
        $optimizedStream = $mozjpegOptimizer->optimize($stream);

        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize > 0);
        $this->assertTrue($optimizerStreamSize < $sourceStreamSize);

        $fs = new Filesystem();
        $this->assertTrue($fs->isJpg($optimizedStream));

        $fs->remove(stream_get_meta_data($optimizedStream)['uri']);
    }

    public function testOptimizeAPNG()
    {
        $src = FIXTURES_DIR . '/test.png';
        $stream = fopen($src, 'r');

        $mozjpegOptimizer = new GuetzliOptimizer();
        $optimizedStream = $mozjpegOptimizer->optimize($stream);

        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize > 0);
        $this->assertTrue($optimizerStreamSize < $sourceStreamSize);

        $fs = new Filesystem();
        $this->assertTrue($fs->isJpg($optimizedStream));

        $fs->remove(stream_get_meta_data($optimizedStream)['uri']);
    }

    public function testSupportGuetzliOptions()
    {
        $src = FIXTURES_DIR . '/test.jpg';
        $stream = fopen($src, 'r');

        $mozjpegOptimizer = new GuetzliOptimizer([
            'quality' => 84
        ]);
        $optimizedStream = $mozjpegOptimizer->optimize($stream);
        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize > 0);
        $this->assertTrue($optimizerStreamSize < $sourceStreamSize);

        $fs = new Filesystem();
        $this->assertTrue($fs->isJpg($optimizedStream));

        $fs->remove(stream_get_meta_data($optimizedStream)['uri']);
    }

    public function testThrowErrorWhenInputIsNotResource()
    {
        $this->expectException(\Exception::class);

        $mozjpegOptimizer = new GuetzliOptimizer();
        $mozjpegOptimizer->optimize('String');
    }

    public function testSkipOptimizingANonPNGAndJPGFile()
    {
        $src = FIXTURES_DIR . '/test.gif';
        $stream = fopen($src, 'r');

        $mozjpegOptimizer = new GuetzliOptimizer();
        $optimizedStream = $mozjpegOptimizer->optimize($stream);

        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize == $sourceStreamSize);
    }

    public function testThrowErrorWhenAJPGIsCorrupt()
    {
        $this->expectException(\Exception::class);

        $src = FIXTURES_DIR . '/test-corrupt.jpg';
        $stream = fopen($src, 'r');

        $mozjpegOptimizer = new GuetzliOptimizer();
        $mozjpegOptimizer->optimize($stream);
    }
}

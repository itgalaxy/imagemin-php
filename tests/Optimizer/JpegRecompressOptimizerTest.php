<?php
namespace Itgalaxy\Imagemin\Optimizer\Tests;

use Itgalaxy\Imagemin\Filesystem\Filesystem;
use Itgalaxy\Imagemin\Optimizer\JpegRecompressOptimizer;
use PHPUnit\Framework\TestCase;

class JpegRecompressOptimizerTest extends TestCase
{
    public function testOptimizeAJPG()
    {
        $src = FIXTURES_DIR . '/test.jpg';
        $stream = fopen($src, 'r');

        $jpegRecompressOptimizer = new JpegRecompressOptimizer();
        $optimizedStream = $jpegRecompressOptimizer->optimize($stream);

        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize > 0);
        $this->assertTrue($optimizerStreamSize < $sourceStreamSize);

        $fs = new Filesystem();
        $this->assertTrue($fs->isJPG($optimizedStream));
        $this->assertTrue($fs->isProgressiveJPG($optimizedStream));

        $fs->remove(stream_get_meta_data($optimizedStream)['uri']);
    }

    public function testSupportJpegoptimOptions()
    {
        $src = FIXTURES_DIR . '/test.jpg';
        $stream = fopen($src, 'r');

        $jpegRecompressOptimizer = new JpegRecompressOptimizer([
            'accurate' => true,
            'quality' => 'low',
            'method' => 'ssim',
            'target' => 0.9999,
            'min' => 40,
            'max' => 95,
            'loops' => 6,
            'defish' => 0,
            'zoom' => 0,
            'progressive' => false,
            'subsample' => 'default',
            'strip' => false
        ]);
        $optimizedStream = $jpegRecompressOptimizer->optimize($stream);

        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize > 0);
        $this->assertTrue($optimizerStreamSize < $sourceStreamSize);

        $fs = new Filesystem();
        $this->assertTrue($fs->isJPG($optimizedStream));
        $this->assertFalse($fs->isProgressiveJPG($optimizedStream));

        $fs->remove(stream_get_meta_data($optimizedStream)['uri']);
    }

    public function testThrowErrorWhenInputIsNotResource()
    {
        $this->expectException(\Exception::class);

        $jpegRecompressOptimizer = new JpegRecompressOptimizer();
        $jpegRecompressOptimizer->optimize('String');
    }

    public function testSkipOptimizingANonJPGFile()
    {
        $src = FIXTURES_DIR . '/test.png';
        $stream = fopen($src, 'r');

        $jpegRecompressOptimizer = new JpegRecompressOptimizer();
        $optimizedStream = $jpegRecompressOptimizer->optimize($stream);

        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize == $sourceStreamSize);
    }

    public function testThrowErrorWhenAJPGIsCorrupt()
    {
        $this->expectException(\Exception::class);

        $src = FIXTURES_DIR . '/test-corrupt.jpg';
        $stream = fopen($src, 'r');

        $jpegRecompressOptimizer = new JpegRecompressOptimizer();
        $jpegRecompressOptimizer->optimize($stream);
    }
}

<?php
namespace Itgalaxy\Imagemin\Optimizer\Tests;

use Itgalaxy\Imagemin\Filesystem\Filesystem;
use Itgalaxy\Imagemin\Optimizer\MozjpegOptimizer;
use PHPUnit\Framework\TestCase;

class MozjpegOptimizerTest extends TestCase
{
    public function testOptimizeAJPG()
    {
        $src = FIXTURES_DIR . '/test.jpg';
        $stream = fopen($src, 'r');

        $mozjpegOptimizer = new MozjpegOptimizer();
        $optimizedStream = $mozjpegOptimizer->optimize($stream);

        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize > 0);
        $this->assertTrue($optimizerStreamSize < $sourceStreamSize);

        $fs = new Filesystem();
        $this->assertTrue($fs->isJPG($optimizedStream));
        $this->assertTrue($fs->isProgressiveJPG($optimizedStream));

        $fs->remove(stream_get_meta_data($optimizedStream)['uri']);
    }

    public function testSupportMozjpegOptions()
    {
        $src = FIXTURES_DIR . '/test.jpg';
        $stream = fopen($src, 'r');

        $mozjpegOptimizer = new MozjpegOptimizer([
            'quality' => 0,
            'progressive' => false,
            'revert' => true,
            'fastcrush' => true,
            'dcScanOpt'=> 2,
            'notrellis' => true,
            'notrellisDC' => true,
            'tune' => 'psnr',
            'noovershoot' =>true,
            'arithmetic' => true,
            'dct' => 'fast',
            'quantTable' => 0,
            'smooth' => 10,
            'maxmemory' => 1024
        ]);
        $optimizedStream = $mozjpegOptimizer->optimize($stream);

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

        $mozjpegOptimizer = new MozjpegOptimizer();
        $mozjpegOptimizer->optimize('String');
    }

    public function testSkipOptimizingANonJPGFile()
    {
        $src = FIXTURES_DIR . '/test.png';
        $stream = fopen($src, 'r');

        $mozjpegOptimizer = new MozjpegOptimizer();
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

        $mozjpegOptimizer = new MozjpegOptimizer();
        $mozjpegOptimizer->optimize($stream);
    }
}

<?php
namespace Itgalaxy\Imagemin\Optimizer\Tests;

use Itgalaxy\Imagemin\Filesystem\Filesystem;
use Itgalaxy\Imagemin\Optimizer\JpegoptimOptimizer;
use PHPUnit\Framework\TestCase;

class JpegoptimOptimizerTest extends TestCase
{
    public function testOptimizeAJPG()
    {
        $src = FIXTURES_DIR . '/test.jpg';
        $stream = fopen($src, 'r');

        $jpegoptimOptimizer = new JpegoptimOptimizer();
        $optimizedStream = $jpegoptimOptimizer->optimize($stream);

        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize > 0);
        $this->assertTrue($optimizerStreamSize < $sourceStreamSize);

        $fs = new Filesystem();
        $this->assertTrue($fs->isJPG($optimizedStream));
        $this->assertFalse($fs->isProgressiveJPG($optimizedStream));

        $fs->remove(stream_get_meta_data($optimizedStream)['uri']);
    }

    public function testSupportJpegoptimOptions()
    {
        $src = FIXTURES_DIR . '/test.jpg';
        $stream = fopen($src, 'r');

        $jpegoptimOptimizer = new JpegoptimOptimizer([
            'progressive' => true,
            'max' => 100,
            'size' => '1%-%99'
        ]);
        $optimizedStream = $jpegoptimOptimizer->optimize($stream);

        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize > 0);
        $this->assertTrue($optimizerStreamSize > $sourceStreamSize);

        $fs = new Filesystem();
        $this->assertTrue($fs->isJPG($optimizedStream));
        // Todo why?
        // $this->assertTrue($fs->isProgressiveJPG($optimizedStream));

        $fs->remove(stream_get_meta_data($optimizedStream)['uri']);
    }

    public function testThrowErrorWhenInputIsNotResource()
    {
        $this->expectException(\Exception::class);

        $jpegoptimOptimizer = new JpegoptimOptimizer();
        $jpegoptimOptimizer->optimize('String');
    }

    public function testSkipOptimizingANonJPGFile()
    {
        $src = FIXTURES_DIR . '/test.png';
        $stream = fopen($src, 'r');

        $jpegoptimOptimizer = new JpegoptimOptimizer();
        $optimizedStream = $jpegoptimOptimizer->optimize($stream);

        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize == $sourceStreamSize);
    }

    public function testThrowErrorWhenAJPGIsCorrupt()
    {
        $this->expectException(\Exception::class);

        $src = FIXTURES_DIR . '/test-corrupt.jpg';
        $stream = fopen($src, 'r');

        $jpegoptimOptimizer = new JpegoptimOptimizer();
        $jpegoptimOptimizer->optimize($stream);
    }
}

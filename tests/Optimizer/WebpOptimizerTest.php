<?php
namespace Itgalaxy\Imagemin\Optimizer\Tests;

use Itgalaxy\Imagemin\Filesystem\Filesystem;
use Itgalaxy\Imagemin\Optimizer\WebpOptimizer;
use PHPUnit\Framework\TestCase;

class WebpOptimizerTest extends TestCase
{
    public function testConvertAnImageIntoAWebP()
    {
        $src = FIXTURES_DIR . '/test.png';
        $stream = fopen($src, 'r');

        $webpOptimizer = new WebpOptimizer();
        $optimizedStream = $webpOptimizer->optimize($stream);

        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize > 0);
        $this->assertTrue($optimizerStreamSize < $sourceStreamSize);

        $fs = new Filesystem();
        $this->assertTrue($fs->isWebp($optimizedStream));

        $fs->remove(stream_get_meta_data($optimizedStream)['uri']);
    }

    public function testSupportWebpOptions()
    {
        $src = FIXTURES_DIR . '/test.png';
        $stream = fopen($src, 'r');

        $webpOptimizer = new WebpOptimizer([
            'preset' => 'icon',
            'quality' => 75,
            'alphaQuality' => 100,
            'method' => 4,
            'size'=> 2000,
            'sns' => 80,
            'filter' => 0,
            'autoFilter' => true,
            'sharpness' => 0,
            'lossless' => true
        ]);
        $optimizedStream = $webpOptimizer->optimize($stream);

        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize > 0);
        $this->assertTrue($optimizerStreamSize < $sourceStreamSize);

        $fs = new Filesystem();
        $this->assertTrue($fs->isWebp($optimizedStream));

        $fs->remove(stream_get_meta_data($optimizedStream)['uri']);
    }

    public function testSkipOptimizingUnsupportedFiles()
    {
        $src = FIXTURES_DIR . '/test-unsupported.bmp';
        $stream = fopen($src, 'r');

        $webpOptimizer = new WebpOptimizer();
        $optimizedStream = $webpOptimizer->optimize($stream);

        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize == $sourceStreamSize);
    }

    public function testThrowErrorWhenInputIsNotResource()
    {
        $this->expectException(\Exception::class);

        $webpOptimizer = new WebpOptimizer();
        $webpOptimizer->optimize('String');
    }

    // Todo add to all tests throw error when an image is corrupt
    public function testThrowErrorWhenAnImageIsCorrupt()
    {
        $this->expectException(\Exception::class);

        $src = FIXTURES_DIR . '/test-corrupt.webp';
        $stream = fopen($src, 'r');

        $webpOptimizer = new WebpOptimizer();
        $webpOptimizer->optimize($stream);
    }
}

<?php
namespace Itgalaxy\Imagemin\Optimizer\Tests;

use Itgalaxy\Imagemin\Filesystem\Filesystem;
use Itgalaxy\Imagemin\Optimizer\PngquantOptimizer;
use PHPUnit\Framework\TestCase;

class PngquantOptimizerTest extends TestCase
{
    public function testOptimizeAPNG()
    {
        $src = FIXTURES_DIR . '/test.png';
        $stream = fopen($src, 'r');

        $optipngOptimizer = new PngquantOptimizer();
        $optimizedStream = $optipngOptimizer->optimize($stream);

        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize < $sourceStreamSize);

        $fs = new Filesystem();
        $this->assertTrue($fs->isPNG($optimizedStream));
    }

    public function testSupportOptipngOptions()
    {
        $src = FIXTURES_DIR . '/test.png';
        $stream = fopen($src, 'r');

        $optipngOptimizer = new PngquantOptimizer([
            'floyd' => 1,
            'nofs' => true,
            'posterize' => 4,
            'quality' => '50',
            'speed' => 1,
            'verbose' => true
        ]);
        $optimizedStream = $optipngOptimizer->optimize($stream);
        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize < $sourceStreamSize);

        $fs = new Filesystem();
        $this->assertTrue($fs->isPNG($optimizedStream));
    }

    public function testSupportOptipngOptionsFloydBool()
    {
        $src = FIXTURES_DIR . '/test.png';
        $stream = fopen($src, 'r');

        $optipngOptimizer = new PngquantOptimizer([
            'floyd' => true,
            'nofs' => true,
            'posterize' => 4,
            'quality' => '50',
            'speed' => 1,
            'verbose' => true
        ]);
        $optimizedStream = $optipngOptimizer->optimize($stream);
        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize < $sourceStreamSize);

        $fs = new Filesystem();
        $this->assertTrue($fs->isPNG($optimizedStream));
    }

    public function testThrowErrorWhenInputIsNotResource()
    {
        $this->expectException(\Exception::class);

        $optipngOptimizer = new PngquantOptimizer();
        $optipngOptimizer->optimize('String');
    }

    public function testSkipOptimizingANonPngFile()
    {
        $src = FIXTURES_DIR . '/test.jpg';
        $stream = fopen($src, 'r');

        $optipngOptimizer = new PngquantOptimizer();
        $optimizedStream = $optipngOptimizer->optimize($stream);

        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize == $sourceStreamSize);
    }
}

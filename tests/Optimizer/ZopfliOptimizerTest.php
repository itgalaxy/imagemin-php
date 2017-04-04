<?php
namespace Itgalaxy\Imagemin\Optimizer\Tests;

use Itgalaxy\Imagemin\Filesystem\Filesystem;
use Itgalaxy\Imagemin\Optimizer\ZopfliOptimizer;
use PHPUnit\Framework\TestCase;

class ZopfliOptimizerTest extends TestCase
{
    public function testOptimizeAPNG()
    {
        $src = FIXTURES_DIR . '/test.png';
        $stream = fopen($src, 'r');

        $optipngOptimizer = new ZopfliOptimizer();
        $optimizedStream = $optipngOptimizer->optimize($stream);

        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize < $sourceStreamSize);

        $fs = new Filesystem();
        $this->assertTrue($fs->isPng($optimizedStream));
    }

    public function testSupportZopfliOptions()
    {
        $src = FIXTURES_DIR . '/test.png';
        $stream = fopen($src, 'r');

        $optipngOptimizer = new ZopfliOptimizer([
            '8bit' => true,
            'transparent' => true,
            'iterations' => 25,
            // Todo why?
            // 'iterationsLarge' => 15,
            'more' => true
        ]);
        $optimizedStream = $optipngOptimizer->optimize($stream);
        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize < $sourceStreamSize);

        $fs = new Filesystem();
        $this->assertTrue($fs->isPng($optimizedStream));
    }

    public function testThrowErrorWhenInputIsNotResource()
    {
        $this->expectException(\Exception::class);

        $optipngOptimizer = new ZopfliOptimizer();
        $optipngOptimizer->optimize('String');
    }

    public function testSkipOptimizingANonPngFile()
    {
        $src = FIXTURES_DIR . '/test.jpg';
        $stream = fopen($src, 'r');

        $optipngOptimizer = new ZopfliOptimizer();
        $optimizedStream = $optipngOptimizer->optimize($stream);

        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize == $sourceStreamSize);
    }
}

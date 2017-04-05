<?php
namespace Itgalaxy\Imagemin\Optimizer\Tests;

use Itgalaxy\Imagemin\Filesystem\Filesystem;
use Itgalaxy\Imagemin\Optimizer\JpegtranOptimizer;
use PHPUnit\Framework\TestCase;

class JpegtranOptimizerTest extends TestCase
{
    public function testOptimizeAJPG()
    {
        $src = FIXTURES_DIR . '/test.jpg';
        $stream = fopen($src, 'r');

        $jpegtranOptimizer = new JpegtranOptimizer();
        $optimizedStream = $jpegtranOptimizer->optimize($stream);

        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize > 0);
        $this->assertTrue($optimizerStreamSize < $sourceStreamSize);

        $fs = new Filesystem();
        $this->assertTrue($fs->isJPG($optimizedStream));
        $this->assertFalse($fs->isProgressiveJPG($optimizedStream));

        $fs->remove(stream_get_meta_data($optimizedStream)['uri']);
    }

    public function testSupportJpegtranOptionsProgressive()
    {
        $src = FIXTURES_DIR . '/test.jpg';
        $stream = fopen($src, 'r');

        $jpegtranOptimizer = new JpegtranOptimizer([
            'progressive' => true
        ]);
        $optimizedStream = $jpegtranOptimizer->optimize($stream);
        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize > 0);
        $this->assertTrue($optimizerStreamSize < $sourceStreamSize);

        $fs = new Filesystem();
        $this->assertTrue($fs->isJPG($optimizedStream));
        $this->assertTrue($fs->isProgressiveJPG($optimizedStream));

        $fs->remove(stream_get_meta_data($optimizedStream)['uri']);
    }

    public function testSupportJpegtranOptionsArithmetic()
    {
        $src = FIXTURES_DIR . '/test.jpg';
        $stream = fopen($src, 'r');

        $jpegtranOptimizer = new JpegtranOptimizer([
            'arithmetic' => true
        ]);
        $optimizedStream = $jpegtranOptimizer->optimize($stream);
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

        $jpegtranOptimizer = new JpegtranOptimizer();
        $jpegtranOptimizer->optimize('String');
    }

    public function testSkipOptimizingANonJPGFile()
    {
        $src = FIXTURES_DIR . '/test.png';
        $stream = fopen($src, 'r');

        $jpegtranOptimizer = new JpegtranOptimizer();
        $optimizedStream = $jpegtranOptimizer->optimize($stream);

        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize == $sourceStreamSize);
    }

    public function testThrowErrorWhenAJPGIsCorrupt()
    {
        $this->expectException(\Exception::class);

        $src = FIXTURES_DIR . '/test-corrupt.jpg';
        $stream = fopen($src, 'r');

        $jpegtranOptimizer = new JpegtranOptimizer();
        $jpegtranOptimizer->optimize($stream);
    }
}

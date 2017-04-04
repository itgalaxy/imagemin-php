<?php
namespace Itgalaxy\Imagemin\Optimizer\Tests;

use Itgalaxy\Imagemin\Optimizer\NullOptimizer;
use PHPUnit\Framework\TestCase;

class NullOptimizerTest extends TestCase
{
    public function testOptimizeAJPG()
    {
        $src = FIXTURES_DIR . '/test.png';
        $stream = fopen($src, 'r');

        $mozjpegOptimizer = new NullOptimizer();
        $optimizedStream = $mozjpegOptimizer->optimize($stream);

        $sourceStreamSize = fstat($stream)['size'];
        $optimizerStreamSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizerStreamSize == $sourceStreamSize);
    }

    public function testThrowErrorWhenInputIsNotResource()
    {
        $this->expectException(\Exception::class);

        $mozjpegOptimizer = new NullOptimizer();
        $mozjpegOptimizer->optimize('String');
    }
}

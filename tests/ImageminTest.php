<?php
namespace Itgalaxy\Imagemin\Tests;

use Itgalaxy\Imagemin\Imagemin;
use Itgalaxy\Imagemin\Optimizer\JpegtranOptimizer;
use PHPUnit\Framework\TestCase;

class ImageminTest extends TestCase
{
    public function testOptimizeAFile()
    {
        $file = __DIR__ . '/fixtures/fixture.jpg';
        $imagemin = new Imagemin([
            'plugins' => new JpegtranOptimizer()
        ]);

        $optimizedStream = $imagemin->process($file);

        $originalSize = filesize($file);
        $optimizedSize = fstat($optimizedStream[$file])['size'];

        $this->assertTrue($optimizedSize < $originalSize);
    }
}

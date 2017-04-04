<?php
namespace Itgalaxy\Imagemin\Tests;

use Itgalaxy\Imagemin\Filesystem\Filesystem;
use Itgalaxy\Imagemin\Imagemin;
use Itgalaxy\Imagemin\Optimizer\ChainOptimizer;
use Itgalaxy\Imagemin\Optimizer\JpegtranOptimizer;
use Itgalaxy\Imagemin\Optimizer\OptipngOptimizer;
use Itgalaxy\Imagemin\Optimizer\PngquantOptimizer;
use PHPUnit\Framework\TestCase;

class ImageminTest extends TestCase
{
    public function testOptimizeAJPGWithJpegtran()
    {
        $file = __DIR__ . '/fixtures/test.jpg';
        $imagemin = new Imagemin([
            'plugins' => new JpegtranOptimizer()
        ]);

        $optimizedStreams = $imagemin->process($file);

        foreach ($optimizedStreams as $originalPath => $optimizedStream) {
            $originalSize = filesize($originalPath);
            $optimizedSize = fstat($optimizedStream)['size'];

            $this->assertTrue($optimizedSize > 0);
            $this->assertTrue($optimizedSize < $originalSize);

            $fs = new Filesystem();
            $fs->remove(stream_get_meta_data($optimizedStream)['uri']);
        }
    }

    public function testOptimizeAPNGWithPngquantAndOptipng()
    {
        $file = __DIR__ . '/fixtures/test.png';
        $imagemin = new Imagemin([
            'plugins' => new ChainOptimizer([
                new PngquantOptimizer(),
                new OptipngOptimizer()
            ])
        ]);

        $optimizedStreams = $imagemin->process($file);

        foreach ($optimizedStreams as $originalPath => $optimizedStream) {
            $originalSize = filesize($originalPath);
            $optimizedSize = fstat($optimizedStream)['size'];

            $this->assertTrue($optimizedSize > 0);
            $this->assertTrue($optimizedSize < $originalSize);

            $fs = new Filesystem();
            $fs->remove(stream_get_meta_data($optimizedStream)['uri']);
        }
    }

    public function testOptimizeAPNGWithJpegtranWithOutput()
    {
        $file = __DIR__ . '/fixtures/test.jpg';
        $imagemin = new Imagemin([
            'plugins' => new JpegtranOptimizer()
        ]);

        $fs = new Filesystem();
        $output = $fs->getTempDir();
        $optimizedStreams = $imagemin->process($file, $output);

        foreach ($optimizedStreams as $originalPath => $optimizedStream) {
            $originalSize = filesize($originalPath);
            $optimizedSize = fstat($optimizedStream)['size'];

            $this->assertTrue($optimizedSize > 0);
            $this->assertTrue($optimizedSize < $originalSize);
        }
    }

    public function testProcessStreamAJPGWithJpegtran()
    {
        $file = __DIR__ . '/fixtures/test.jpg';
        $imagemin = new Imagemin([
            'plugins' => new JpegtranOptimizer()
        ]);

        $optimizedStream = $imagemin->processStream(fopen($file, 'r'));

        $originalSize = filesize($file);
        $optimizedSize = fstat($optimizedStream)['size'];

        $this->assertTrue($optimizedSize > 0);
        $this->assertTrue($optimizedSize < $originalSize);
    }

    public function testThrowErrorIfInputNotString()
    {
        $this->expectException(\Exception::class);

        $file = __DIR__ . '/fixtures/not-exists-test.jpg';
        $imagemin = new Imagemin([
            'plugins' => new JpegtranOptimizer()
        ]);

        $imagemin->process([$file]);
    }
}

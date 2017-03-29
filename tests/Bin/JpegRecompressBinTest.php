<?php
namespace Itgalaxy\Imagemin\Bin\Tests;

use Itgalaxy\Imagemin\Bin\JpegRecompressBin;
use Itgalaxy\Imagemin\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;

class JpegRecompressBinTest extends TestCase
{
    // Todo https://github.com/imagemin/gifsicle-bin/blob/master/test/test.js#L23 ? where

    // Todo https://github.com/imagemin/jpeg-recompress-bin/blob/master/test/test.js#L21

    public function testMinifyAJPG()
    {
        $src = FIXTURES_DIR . '/test.jpg';
        $fs = new Filesystem();
        $tempDir = $fs->getTempDir();
        $dest = $tempDir . '/test-optimize-jpeg-recompress.jpg';

        $jpegRecompressBin = new JpegRecompressBin();
        $binPath = $jpegRecompressBin->getBinPath();

        $builder = new ProcessBuilder();
        $process = $builder
            ->setPrefix($binPath)
            ->setArguments([
                '--quality',
                'high',
                '--min',
                '60',
                $src,
                $dest
            ])
            ->getProcess();

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $sourceFileSize = filesize($src);
        $destFileSize = filesize($dest);

        $this->assertTrue($sourceFileSize > $destFileSize);

        $fs->remove($dest);
    }
}

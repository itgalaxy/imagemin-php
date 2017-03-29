<?php
namespace Itgalaxy\Imagemin\Bin\Tests;

use Itgalaxy\Imagemin\Bin\GifsicleBin;
use Itgalaxy\Imagemin\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;

class GifsicleBinTest extends TestCase
{
    // Todo https://github.com/imagemin/gifsicle-bin/blob/master/test/test.js#L23

    // Todo https://github.com/imagemin/gifsicle-bin/blob/master/test/test.js#L45

    public function testMinifyAGIF()
    {
        $src = FIXTURES_DIR . '/test.gif';
        $fs = new Filesystem();
        $tempDir = $fs->getTempDir();
        $dest = $tempDir . '/test-optimize-gifsicle.gif';

        $gifsicleBin = new GifsicleBin();
        $binPath = $gifsicleBin->getBinPath();

        $builder = new ProcessBuilder();
        $process = $builder
            ->setPrefix($binPath)
            ->setArguments([
                '-o',
                $dest,
                $src
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

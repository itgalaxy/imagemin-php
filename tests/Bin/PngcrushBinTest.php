<?php
namespace Itgalaxy\Imagemin\Bin\Tests;

use Itgalaxy\Imagemin\Bin\PngcrushBin;
use Itgalaxy\Imagemin\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;

class PngcrushBinTest extends TestCase
{
    // Todo https://github.com/imagemin/pngcrush-bin/blob/master/test/test.js#L24

    // Todo https://github.com/imagemin/pngcrush-bin/blob/master/test/test.js#L36

    public function testMinifyAPNG()
    {
        $src = FIXTURES_DIR . '/test.png';
        $fs = new Filesystem();
        $tempDir = $fs->getTempDir();
        $dest = $tempDir . '/test-optimize-pngcrush.png';

        $pngcrushBin = new PngcrushBin();
        $binPath = $pngcrushBin->getBinPath();

        $builder = new ProcessBuilder();
        $process = $builder
            ->setPrefix($binPath)
            ->setArguments([
                '-reduce',
                '-brute',
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

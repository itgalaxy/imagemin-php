<?php
namespace Itgalaxy\Imagemin\Bin\Tests;

use Itgalaxy\Imagemin\Bin\PngquantBin;
use Itgalaxy\Imagemin\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;

class PngquantBinTest extends TestCase
{
    // Todo https://github.com/imagemin/pngquant-bin/blob/master/test/test.js#L12

    // Todo https://github.com/imagemin/pngquant-bin/blob/master/test/test.js#L27

    public function testMinifyAPNG()
    {
        $src = FIXTURES_DIR . '/test.png';
        $fs = new Filesystem();
        $tempDir = $fs->getTempDir();
        $dest = $tempDir . '/test-optimize-pngquant.png';

        $pngquantBin = new PngquantBin();
        $binPath = $pngquantBin->getBinPath();

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

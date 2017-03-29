<?php
namespace Itgalaxy\Imagemin\Bin\Tests;

use Itgalaxy\Imagemin\Bin\MozjpegBin;
use Itgalaxy\Imagemin\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;

class MozjpegBinTest extends TestCase
{
    // Todo https://github.com/imagemin/mozjpeg-bin/blob/master/test/test.js#L25

    // Todo https://github.com/imagemin/mozjpeg-bin/blob/master/test/test.js#L51

    public function testMinifyAJPG()
    {
        $src = FIXTURES_DIR . '/test.jpg';
        $fs = new Filesystem();
        $tempDir = $fs->getTempDir();
        $dest = $tempDir . '/test-optimize-mozjpeg.jpg';

        $mozjpegBin = new MozjpegBin();
        $binPath = $mozjpegBin->getBinPath();

        $builder = new ProcessBuilder();
        $process = $builder
            ->setPrefix($binPath)
            ->setArguments([
                '-outfile',
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

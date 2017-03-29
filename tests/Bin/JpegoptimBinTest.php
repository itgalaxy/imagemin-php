<?php
namespace Itgalaxy\Imagemin\Bin\Tests;

use Itgalaxy\Imagemin\Bin\JpegoptimBin;
use Itgalaxy\Imagemin\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;

class JpegoptimBinTest extends TestCase
{
    // Todo https://github.com/imagemin/jpegoptim-bin/blob/master/test/test.js#L23

    // Todo https://github.com/imagemin/jpegoptim-bin/blob/master/test/test.js#L35

    public function testMinifyAJPG()
    {
        $src = FIXTURES_DIR . '/test.jpg';
        $fs = new Filesystem();
        $tempDir = $fs->getTempDir();
        $dest = $tempDir . '/test.jpg';

        $jpegoptimBin = new JpegoptimBin();
        $binPath = $jpegoptimBin->getBinPath();

        $builder = new ProcessBuilder();
        $process = $builder
            ->setPrefix($binPath)
            ->setArguments([
                '--strip-all',
                '--all-progressive',
                '--dest=' . $tempDir,
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

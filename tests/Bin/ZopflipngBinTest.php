<?php
namespace Itgalaxy\Imagemin\Bin\Tests;

use Itgalaxy\Imagemin\Bin\ZopflipngBin;
use Itgalaxy\Imagemin\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;

class ZopflipngBinTest extends TestCase
{
    // Todo https://github.com/imagemin/zopflipng-bin/blob/master/test/test.js#L24

    // Todo https://github.com/imagemin/zopflipng-bin/blob/master/test/test.js#L38

    public function testMinifyAPNG()
    {
        $src = FIXTURES_DIR . '/test.png';
        $fs = new Filesystem();
        $tempDir = $fs->getTempDir();
        $dest = $tempDir . '/test-optimize-zopflipng.png';

        $zopflipngBin = new ZopflipngBin();
        $binPath = $zopflipngBin->getBinPath();

        $builder = new ProcessBuilder();
        $process = $builder
            ->setPrefix($binPath)
            ->setArguments([
                '--lossy_8bit',
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

<?php
namespace Itgalaxy\Imagemin\Bin\Tests;

use Itgalaxy\Imagemin\Bin\OptipngBin;
use Itgalaxy\Imagemin\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;

class OptipngBinTest extends TestCase
{
    // Todo https://github.com/imagemin/optipng-bin/blob/master/test/test.js#L23

    // Todo https://github.com/imagemin/optipng-bin/blob/master/test/test.js#L39

    public function testMinifyAPNG()
    {
        $src = FIXTURES_DIR . '/test.png';
        $fs = new Filesystem();
        $tempDir = $fs->getTempDir();
        $dest = $tempDir . '/test-optimize-optipng.png';

        $optipngBin = new OptipngBin();
        $binPath = $optipngBin->getBinPath();

        $builder = new ProcessBuilder();
        $process = $builder
            ->setPrefix($binPath)
            ->setArguments([
                '-strip',
                'all',
                '-clobber',
                '-out',
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

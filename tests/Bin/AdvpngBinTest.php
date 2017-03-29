<?php
namespace Itgalaxy\Imagemin\Bin\Tests;

use Itgalaxy\Imagemin\Bin\AdvpngBin;
use Itgalaxy\Imagemin\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;

class AdvpngBinTest extends TestCase
{
    // Todo https://github.com/imagemin/advpng-bin/blob/master/test/test.js#L23

    // Todo https://github.com/imagemin/advpng-bin/blob/master/test/test.js#L37

    public function testMinifyAPNG()
    {
        $src = FIXTURES_DIR . '/test.png';
        $fs = new Filesystem();
        $tempDir = $fs->getTempDir();
        $dest = $tempDir . '/test-optimize-advpng.png';

        $fs->copy($src, $dest);

        $advpngBin = new AdvpngBin();
        $binPath = $advpngBin->getBinPath();

        $builder = new ProcessBuilder();
        $process = $builder
            ->setPrefix($binPath)
            ->setArguments([
                '--recompress',
                '--shrink-extra',
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

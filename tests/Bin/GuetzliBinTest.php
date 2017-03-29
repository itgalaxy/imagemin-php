<?php
namespace Itgalaxy\Imagemin\Bin\Tests;

use Itgalaxy\Imagemin\Bin\GuetzliBin;
use Itgalaxy\Imagemin\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;

class GuetzliBinTest extends TestCase
{
    // Todo https://github.com/imagemin/guetzli-bin/blob/master/test/test.js#L22

    // Todo https://github.com/imagemin/guetzli-bin/blob/master/test/test.js#L38

    public function testMinifyAJPG()
    {
        $src = FIXTURES_DIR . '/test.jpg';
        $fs = new Filesystem();
        $tempDir = $fs->getTempDir();
        $dest = $tempDir . '/test-optimize-guetzli.jpg';

        $guetzliBin = new GuetzliBin();
        $binPath = $guetzliBin->getBinPath();

        $builder = new ProcessBuilder();
        $process = $builder
            ->setPrefix($binPath)
            ->setArguments([
                '--quality',
                '84',
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

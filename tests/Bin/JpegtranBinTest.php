<?php
namespace Itgalaxy\Imagemin\Bin\Tests;

use Itgalaxy\Imagemin\Bin\JpegtranBin;
use Itgalaxy\Imagemin\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;

class JpegtranBinTest extends TestCase
{
    // Todo https://github.com/imagemin/jpegtran-bin/blob/master/test/test.js#L23

    // Todo https://github.com/imagemin/jpegtran-bin/blob/master/test/test.js#L44

    public function testMinifyAJPG()
    {
        $src = FIXTURES_DIR . '/test.jpg';
        $fs = new Filesystem();
        $tempDir = $fs->getTempDir();
        $dest = $tempDir . '/test-optimize-jpegtran.jpg';

        $jpegtranBin = new JpegtranBin();
        $binPath = $jpegtranBin->getBinPath();

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

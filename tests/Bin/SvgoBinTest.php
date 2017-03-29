<?php
namespace Itgalaxy\Imagemin\Bin\Tests;

use Itgalaxy\Imagemin\Bin\SvgoBin;
use Itgalaxy\Imagemin\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;

class SvgoBinTest extends TestCase
{
    // Todo https://github.com/imagemin/pngquant-bin/blob/master/test/test.js#L12

    // Todo https://github.com/imagemin/pngquant-bin/blob/master/test/test.js#L27

    // Maybe Todo node_modules in root and only link on bin
    public function testMinifyASVG()
    {
        $src = FIXTURES_DIR . '/test.svg';
        $fs = new Filesystem();
        $tempDir = $fs->getTempDir();
        $dest = $tempDir . '/test-optimize-svgo.svg';

        $svgoBin = new SvgoBin();
        $binPath = $svgoBin->getBinPath();

        $builder = new ProcessBuilder();
        $process = $builder
            ->setPrefix($binPath)
            ->setArguments([
                '--input',
                $src,
                '--output',
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

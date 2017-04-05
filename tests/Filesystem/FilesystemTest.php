<?php
namespace Itgalaxy\Imagemin\Filesystem\Tests;

use Itgalaxy\Imagemin\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;

class FilesystemTest extends TestCase
{
    private $umask;

    protected $longPathNamesWindows = [];

    /**
     * @var Filesystem
     */
    protected $filesystem = null;

    /**
     * @var string
     */
    protected $workspace = null;

    /**
     * @var null|bool Flag for hard links on Windows
     */
    private static $linkOnWindows = null;

    /**
     * @var null|bool Flag for symbolic links on Windows
     */
    private static $symlinkOnWindows = null;

    public static function setUpBeforeClass()
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            self::$linkOnWindows = true;

            $originFile = tempnam(sys_get_temp_dir(), 'li');
            $targetFile = tempnam(sys_get_temp_dir(), 'li');

            if (true !== @link($originFile, $targetFile)) {
                $report = error_get_last();

                if (is_array($report) && false !== strpos($report['message'], 'error code(1314)')) {
                    self::$linkOnWindows = false;
                }
            } else {
                @unlink($targetFile);
            }

            self::$symlinkOnWindows = true;

            $originDir = tempnam(sys_get_temp_dir(), 'sl');
            $targetDir = tempnam(sys_get_temp_dir(), 'sl');

            if (true !== @symlink($originDir, $targetDir)) {
                $report = error_get_last();

                if (is_array($report) && false !== strpos($report['message'], 'error code(1314)')) {
                    self::$symlinkOnWindows = false;
                }
            } else {
                @unlink($targetDir);
            }
        }
    }

    protected function setUp()
    {
        $this->umask = umask(0);
        $this->filesystem = new Filesystem();
        $this->workspace = sys_get_temp_dir() . '/' . microtime(true) . '.' . mt_rand();

        mkdir($this->workspace, 0777, true);

        $this->workspace = realpath($this->workspace);
    }

    protected function tearDown()
    {
        if (!empty($this->longPathNamesWindows)) {
            foreach ($this->longPathNamesWindows as $path) {
                exec('DEL ' . $path);
            }

            $this->longPathNamesWindows = [];
        }

        $this->filesystem->remove($this->workspace);

        umask($this->umask);
    }

    // Todo need external package based on https://github.com/sindresorhus/file-type
    public function testIsJPG()
    {
        $jpgSrc = FIXTURES_DIR . '/test.jpg';

        $this->assertTrue($this->filesystem->isJPG($jpgSrc));
    }

    public function testIsProgressiveJPG()
    {
        $progressiveSrc = FIXTURES_DIR . '/progressive.jpg';
        $curiousExifSrc = FIXTURES_DIR . '/curious-exif.jpg';
        $baseline = FIXTURES_DIR . '/baseline.jpg';

        $this->assertTrue($this->filesystem->isProgressiveJPG($progressiveSrc));
        $this->assertTrue($this->filesystem->isProgressiveJPG($curiousExifSrc));
        $this->assertFalse($this->filesystem->isProgressiveJPG($baseline));
    }

    public function testIsPNG()
    {
        $pngSrc = FIXTURES_DIR . '/test.png';

        $this->assertTrue($this->filesystem->isPNG($pngSrc));
    }

    public function testIsGIF()
    {
        $gifSrc = FIXTURES_DIR . '/test.gif';

        $this->assertTrue($this->filesystem->isGIF($gifSrc));
    }

    public function testIsTIF()
    {
        $tifBigEndianSrc = FIXTURES_DIR . '/big-endian.tif';
        $tifLittleEndianSrc = FIXTURES_DIR . '/little-endian.tif';

        $this->assertTrue($this->filesystem->isTIF($tifBigEndianSrc));
        $this->assertTrue($this->filesystem->isTIF($tifLittleEndianSrc));
    }

    public function testIsWebP()
    {
        $webpSrc = FIXTURES_DIR . '/test.webp';

        $this->assertTrue($this->filesystem->isWebP($webpSrc));
    }

    public function testIsSVG()
    {
        $srcSrc = FIXTURES_DIR . '/test.svg';

        $this->assertTrue($this->filesystem->isSVG($srcSrc));
    }
}

<?php
define('FIXTURES_DIR', __DIR__ . '/fixtures');

/*
 * Require Composer autoloader if installed on it's own.
 */
$autoloader = __DIR__ . '/../vendor/autoload.php';

if (!file_exists($autoloader)) {
    trigger_error('You must run `composer install` from the Insteria directory.', E_USER_ERROR);
}

require_once $autoloader;

use Itgalaxy\Imagemin\Bin\AdvpngBin;
use Itgalaxy\Imagemin\Bin\CwebpBin;
use Itgalaxy\Imagemin\Bin\GifsicleBin;
use Itgalaxy\Imagemin\Bin\GuetzliBin;
use Itgalaxy\Imagemin\Bin\JpegRecompressBin;
use Itgalaxy\Imagemin\Bin\JpegoptimBin;
use Itgalaxy\Imagemin\Bin\JpegtranBin;
use Itgalaxy\Imagemin\Bin\MozjpegBin;
use Itgalaxy\Imagemin\Bin\OptipngBin;
use Itgalaxy\Imagemin\Bin\PngcrushBin;
use Itgalaxy\Imagemin\Bin\PngoutBin;
use Itgalaxy\Imagemin\Bin\PngquantBin;
use Itgalaxy\Imagemin\Bin\ZopflipngBin;

$advpngBin = new AdvpngBin();
$advpngBin->install();

$cwebpBin = new CwebpBin();
$cwebpBin->install();

$gifsicleBin = new GifsicleBin();
$gifsicleBin->install();

$guetzliBin = new GuetzliBin();
$guetzliBin->install();

$jpegRecompressBin = new JpegRecompressBin();
$jpegRecompressBin->install();

$jpegoptimBin = new JpegoptimBin();
$jpegoptimBin->install();

$jpegtranBin = new JpegtranBin();
$jpegtranBin->install();

$mozjpegBin = new MozjpegBin();
$mozjpegBin->install();

$optipngBin = new OptipngBin();
$optipngBin->install();

$pngcrushBin = new PngcrushBin();
$pngcrushBin->install();

$pngoutBin = new PngoutBin();
$pngoutBin->install();

$pngquantBin = new PngquantBin();
$pngquantBin->install();

$zopflipngBin = new ZopflipngBin();
$zopflipngBin->install();

// Todo remove all binaries after test

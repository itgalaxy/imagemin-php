<?php
namespace Itgalaxy\Imagemin\Filesystem;

class Filesystem extends \Symfony\Component\Filesystem\Filesystem
{
    public function isReadable($filename)
    {
        return $this->isReadable($filename);
    }

    public function getTempDir() {
        $temp = '';

        if (function_exists('sys_get_temp_dir')) {
            $temp = sys_get_temp_dir();

            if (@is_dir($temp) && is_writable($temp)) {
                return $temp;
            }
        }

        $temp = ini_get('upload_tmp_dir');

        // Todo wp_is_writable
        if (@is_dir($temp) && is_writable($temp)) {
            return $temp;
        }

        return '/tmp/';
    }

    private function getMimeTypeByFilePath($input)
    {
        if (!file_exists($input)) {
            throw new \Exception('File ' . $input . ' is not found');
        }

        if (!is_readable($input)) {
            throw new \Exception('File ' . $input . ' is not readable');
        }

        if (filesize($input) === 0) {
            throw new \Exception('Empty file ' . $input);
        }

        if (pathinfo($input, PATHINFO_EXTENSION) === 'svg') {
            $isXml = simplexml_load_string(
                file_get_contents($input),
                'SimpleXmlElement',
                LIBXML_NOERROR + LIBXML_ERR_FATAL + LIBXML_ERR_NONE
            );

            if (!$isXml) {
                return false;
            }

            return 'image/svg+xml';
        }

        try {
            if (is_callable('exif_imagetype')) {
                $mime = image_type_to_mime_type(exif_imagetype($input));
            } elseif (function_exists('getimagesize')) {
                $imagesize = getimagesize($input);
                $mime = isset($imagesize['mime']) ? $imagesize['mime'] : false;
            } else {
                $mime = false;
            }
        } catch (\Exception $e) {
            $mime = false;
        }

        return $mime;
    }

    public function getMimeType($filename)
    {
        return mime_content_type($filename);
    }

    public function isJpg($input)
    {
        return true;// $this->getMimeType($input) === 'image/jpeg';
    }
}

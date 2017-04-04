<?php
namespace Itgalaxy\Imagemin\Filesystem;

use Symfony\Component\Filesystem\Exception\IOException;

class Filesystem extends \Symfony\Component\Filesystem\Filesystem
{
    public function isReadable($filename)
    {
        return $this->isReadable($filename);
    }

    public function getTempDir() {
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
        if (!is_resource($input)) {
            $input = stream_get_meta_data($input)['uri'];
        }

        $mimeType = $this->getMimeType($input);

        return $mimeType == 'image/jpeg';
    }

    public function isJpgProgressive($input)
    {
        if (!$this->isJpg($input)) {
            return false;
        }

        if (!is_resource($input)) {
            $input = stream_get_meta_data($input)['uri'];
        }

        $contents = stream_get_contents($input, 65535);
        $bytes = unpack("C*", $contents);

        $prevByte = null;

        foreach ($bytes as $byte) {
            $byte = dechex($byte);

            if ($prevByte !== 'ff') {
                $prevByte = $byte;

                continue;
            }

            if ($byte === 'c2') {
                return true;
            }

            $prevByte = $byte;
        }

        return false;
    }

    public function isPng($input)
    {
        if (!is_resource($input)) {
            $input = stream_get_meta_data($input)['uri'];
        }

        $mimeType = $this->getMimeType($input);

        return $mimeType == 'image/png';
    }

    public function isGif($input)
    {
        if (!is_resource($input)) {
            $input = stream_get_meta_data($input)['uri'];
        }

        $mimeType = $this->getMimeType($input);

        return $mimeType == 'image/gif';
    }

    public function isTif($input)
    {
        if (!is_resource($input)) {
            $input = stream_get_meta_data($input)['uri'];
        }

        $mimeType = $this->getMimeType($input);

        // Todo
        return false;
        // return $mimeType == 'image/webp';
    }

    public function isWebp($input)
    {
        if (!is_resource($input)) {
            $input = stream_get_meta_data($input)['uri'];
        }

        $mimeType = $this->getMimeType($input);

        return $mimeType == 'image/webp';
    }

    public function isSvg($input)
    {
        if (!is_resource($input)) {
            $input = stream_get_meta_data($input)['uri'];
        }

        $mimeType = $this->getMimeType($input);

        return $mimeType == 'image/svg+xml';
    }

    /**
     * Creates a temporary file with support for custom stream wrappers.
     *
     * @param string $dir    The directory where the temporary filename will be created
     * @param string $prefix The prefix of the generated temporary filename
     *                       Note: Windows uses only the first three characters of prefix
     *
     * @return string The new temporary filename (with path), or throw an exception on failure
     */
    public function tempnam($dir, $prefix)
    {
        list($scheme, $hierarchy) = $this->getSchemeAndHierarchy($dir);

        // If no scheme or scheme is "file" or "gs" (Google Cloud) create temp file in local filesystem
        if (null === $scheme || 'file' === $scheme || 'gs' === $scheme) {
            $tmpFile = @tempnam($hierarchy, $prefix);

            // If tempnam failed or no scheme return the filename otherwise prepend the scheme
            if (false !== $tmpFile) {
                if (null !== $scheme && 'gs' !== $scheme) {
                    return $scheme.'://'.$tmpFile;
                }

                return $tmpFile;
            }

            throw new IOException('A temporary file could not be created.');
        }

        // Loop until we create a valid temp file or have reached 10 attempts
        for ($i = 0; $i < 10; ++$i) {
            // Create a unique filename
            $tmpFile = $dir.'/'.$prefix.uniqid(mt_rand(), true);
            // Use fopen instead of file_exists as some streams do not support stat
            // Use mode 'x+' to atomically check existence and create to avoid a TOCTOU vulnerability
            $handle = @fopen($tmpFile, 'x+');

            // If unsuccessful restart the loop
            if (false === $handle) {
                continue;
            }

            // Close the file if it was successfully opened
            @fclose($handle);

            return $tmpFile;
        }

        throw new IOException('A temporary file could not be created.');
    }

    /**
     * Gets a 2-tuple of scheme (may be null) and hierarchical part of a filename (e.g. file:///tmp -> array(file, tmp)).
     *
     * @param string $filename The filename to be parsed
     *
     * @return array The filename scheme and hierarchical part
     */
    private function getSchemeAndHierarchy($filename)
    {
        $components = explode('://', $filename, 2);

        return 2 === count($components)
            ? [$components[0], $components[1]]
            : [null, $components[0]];
    }
}

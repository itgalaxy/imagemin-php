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

    private function getMimeType($originFile)
    {
        if (is_resource($originFile)) {
            $originFile = stream_get_meta_data($originFile)['uri'];
        }

        if (stream_is_local($originFile) && !@is_file($originFile)) {
            throw new \Exception(sprintf('Failed to get mime type "%s" because file does not exist.', $originFile));
        }

        if (filesize($originFile) === 0) {
            throw new \Exception(sprintf('Failed to get mime type "%s" because file is empty.', $originFile));
        }

        try {
            if (is_callable('exif_imagetype')) {
                $mime = image_type_to_mime_type(exif_imagetype($originFile));
            } elseif (function_exists('getimagesize')) {
                $imagesize = getimagesize($originFile);
                $mime = isset($imagesize['mime']) ? $imagesize['mime'] : null;
            } else {
                $mime = null;
            }
        } catch (\Exception $e) {
            $mime = null;
        }

        // Fix for php 5.6
        if (!$mime || $mime === 'application/octet-stream') {
            $contents = file_get_contents($originFile, false, null, 0, 65535);
            $bytes = unpack("C*", $contents);

            if ($bytes[9] == '87' && $bytes[10] == '69' && $bytes[11] == '66' && $bytes[12] == '80') {
                return 'image/webp';
            }

            libxml_use_internal_errors(true);
            $doc = simplexml_load_string($contents);

            if ($doc) {
                libxml_clear_errors();

                return 'image/svg+xml';
            }
        }

        return $mime;
    }

    public function isJPG($originFile)
    {
        return $this->getMimeType($originFile) == 'image/jpeg';
    }

    public function isProgressiveJPG($originFile)
    {
        if (!$this->isJPG($originFile)) {
            return false;
        }

        if (is_resource($originFile)) {
            $originFile = stream_get_meta_data($originFile)['uri'];
        }

        if (stream_is_local($originFile) && !is_file($originFile)) {
            throw new \Exception(sprintf('Failed to get mime type "%s" because file does not exist.', $originFile));
        }

        $stream = @fopen($originFile, 'r');

        if ($stream === false) {
            throw new IOException(
                sprintf(
                    'Failed to check "%s" is progressive jpg because source file could not be opened for reading.',
                    $originFile
                ),
                0,
                null,
                $originFile
            );
        }

        $contents = stream_get_contents($stream, 65535);
        $bytes = unpack("C*", $contents);

        $prevByte = null;

        foreach ($bytes as $byte) {
            $byte = dechex($byte);

            if ($prevByte != 'ff') {
                $prevByte = $byte;

                continue;
            }

            if ($byte == 'c2') {
                return true;
            }

            $prevByte = $byte;
        }

        return false;
    }

    public function isPNG($originFile)
    {
        return $this->getMimeType($originFile) == 'image/png';
    }

    public function isGIF($originFile)
    {
        return $this->getMimeType($originFile) == 'image/gif';
    }

    public function isTIF($originFile)
    {
        return $this->getMimeType($originFile) == 'image/tiff';
    }

    public function isWebP($originFile)
    {
        return $this->getMimeType($originFile) == 'image/webp';
    }

    public function isSVG($originFile)
    {
        return $this->getMimeType($originFile) == 'image/svg+xml';
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
                    return $scheme . '://' . $tmpFile;
                }

                return $tmpFile;
            }

            throw new IOException('A temporary file could not be created.');
        }

        // Loop until we create a valid temp file or have reached 10 attempts
        for ($i = 0; $i < 10; ++$i) {
            // Create a unique filename
            $tmpFile = $dir . '/' . $prefix . uniqid(mt_rand(), true);
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

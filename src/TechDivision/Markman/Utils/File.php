<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_Markman
 * @subpackage Utils
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace TechDivision\Markman\Utils;

/**
 * TechDivision\Markman\Utils\File
 *
 * File utility which provides additional file operation methods.
 *
 * @category   Appserver
 * @package    TechDivision_Markman
 * @subpackage Utils
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
class File
{
    /**
     * Will write content to a file without the need for a existing path to it.
     * If the path and its folders do not exist they will be created.
     *
     * @param string $path     The path to write the content to
     * @param string $contents Content to write into provided file path
     *
     * @return integer
     */
    public function fileForceContents($path, $contents)
    {
        // Split the path into pieces so we can iterate over them
        $parts = explode(DIRECTORY_SEPARATOR, $path);
        $file = array_pop($parts);
        $path = '';

        // Iterate over the path pieces and build the path up directory for directory
        foreach ($parts as $part) {
            if (!is_dir($path .= DIRECTORY_SEPARATOR . $part)) {
                mkdir($path);
            }
        }

        // Finally create our file and fill in the content
        return file_put_contents($path . DIRECTORY_SEPARATOR . $file, $contents);
    }

    /**
     * Will recursively copy a directory path
     *
     * @param string  $src        Source path to copy
     * @param string  $dst        Destination path to copy to
     * @param boolean $forceWrite If true we will use fileForceContents() to even without existing directories
     *
     * @return bool
     */
    public function recursiveCopy($src, $dst, $forceWrite = false)
    {
        // If source is not a directory stop processing
        if (!is_dir($src)) {

            return false;
        }

        // Split the path into pieces so we can iterate over them
        $parts = explode(DIRECTORY_SEPARATOR, $dst);
        $path = '';

        // Iterate over the path pieces and build the path up directory for directory
        foreach ($parts as $part) {
            if (!is_dir($path .= DIRECTORY_SEPARATOR . $part)) {
                mkdir($path);
            }
        }

        // Open the source directory to read in files
        $i = new \DirectoryIterator($src);
        foreach ($i as $f) {
            if ($f->isFile()) {
                copy($f->getRealPath(), "$dst/" . $f->getFilename());
            } else {
                if (!$f->isDot() && $f->isDir()) {
                    $this->recursiveCopy($f->getRealPath(), "$dst/$f");
                }
            }
        }
    }
}

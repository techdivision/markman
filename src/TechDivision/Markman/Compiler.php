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
 * @package    TechDivision
 * @subpackage Markman
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace TechDivision\Markman;

use TechDivision\Markman\Utils\File;

/**
 * TechDivision\Markman\Compiler
 *
 * <TODO CLASS DESCRIPTION>
 *
 * @category   Appserver
 * @package    TechDivision
 * @subpackage Markman
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
class Compiler
{
    /**
     * @var \Parsedown $compiler <REPLACE WITH FIELD COMMENT>
     */
    protected $compiler;

    /**
     * @var  $allowedExtensions <REPLACE WITH FIELD COMMENT>
     */
    protected $allowedExtensions;

    /**
     * @var  $preservedExtensions <REPLACE WITH FIELD COMMENT>
     */
    protected $preservedExtensions;

    protected $config;

    /**
     *
     */
    public function __construct(Config $config)
    {
        $this->config = $config;

        $this->compiler = new \Parsedown();

        $this->allowedExtensions = array_flip(array('md', 'markdown'));

        $this->preservedExtensions = array_flip(array('png', 'jpg', 'jpeg', 'svg', 'css', 'html', 'phtml'));
    }

    /**
     * @param $path
     * @param string $pathModifier
     * @param $filePrefix
     * @return bool
     */
    public function compile($path, $pathModifier = '', $filePrefix)
    {
        // Is there anything useful here?
        if (!is_readable($path . DIRECTORY_SEPARATOR . $pathModifier)) {

            return false;
        } else {

            $path = $path . DIRECTORY_SEPARATOR . $pathModifier;
        }

        // Get an iterator over the part of the directory we want
        $iterator = $this->getDirectoryIterator(array($path));

        // Compile all the files
        $fileUtil = new File();
        foreach ($iterator as $file) {

            // Get the content of the file
            $rawContent = file_get_contents($file);

            // Create the name of the target file
            $targetFile = str_replace($path, '', $file);

            // If the file has a markdown extension we will compile it, if it is something we have to preserve we
            // will do so, if not we will skip it
            if (isset($this->allowedExtensions[$file->getExtension()])) {

                $rawContent = $this->compiler->text($rawContent);
                $targetFile = str_replace($file->getExtension(), 'html', $targetFile);

            } elseif (!isset($this->preservedExtensions[strtolower($file->getExtension())])) {

                continue;
            }

            // Apply any name mapping there might be
            if (count($this->config->getFileMapping()) > 0) {

                // Split up the mapping and apply it
                $haystacks = array_keys($this->config->getFileMapping());
                $targetFile = str_replace($haystacks, $this->config->getFileMapping(), $targetFile);
            }

            // Save the processed (or not) content to a file.
            // Recreate the path the file originally had
            $fileUtil->fileForceContents(
                Constants::BUILD_PATH . DIRECTORY_SEPARATOR . $filePrefix . DIRECTORY_SEPARATOR .
                $targetFile,
                $rawContent
            );
        }
    }

    /**
     * Will Return an iterator over a set of files determined by a list of directories to iterate over
     *
     * @param array $paths List of directories to iterate over
     *
     * @return \Iterator
     */
    protected function getDirectoryIterator(array $paths)
    {

        // As we might have several rootPaths we have to create several RecursiveDirectoryIterators.
        $directoryIterators = array();
        foreach ($paths as $path) {

            $directoryIterators[] = new \RecursiveDirectoryIterator(
                $path,
                \RecursiveDirectoryIterator::SKIP_DOTS
            );
        }

        // We got them all, now append them onto a new RecursiveIteratorIterator and return it.
        $recursiveIterator = new \AppendIterator();
        foreach ($directoryIterators as $directoryIterator) {

            // Append the directory iterator
            $recursiveIterator->append(
                new \RecursiveIteratorIterator(
                    $directoryIterator,
                    \RecursiveIteratorIterator::SELF_FIRST,
                    \RecursiveIteratorIterator::CATCH_GET_CHILD
                )
            );
        }

        return $recursiveIterator;
    }
}

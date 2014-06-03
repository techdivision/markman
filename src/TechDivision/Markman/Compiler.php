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
     *
     */
    public function __construct()
    {
        $this->compiler = new \Parsedown();

        $this->allowedExtensions = array_flip(array('md', 'markdown'));
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

        $iterator = $this->getDirectoryIterator(array($path));

        // Compile all the files
        $fileUtil = new File();
        foreach ($iterator as $file) {

            // If the file has the wrong extension we do not need to compile it
            if (!isset($this->allowedExtensions[$file->getExtension()])) {

                continue;
            }

            $rawContent = file_get_contents($file);
            $fileUtil->fileForceContents(
                Constants::BUILD_PATH . DIRECTORY_SEPARATOR . $filePrefix . DIRECTORY_SEPARATOR . str_replace(
                    $path,
                    '',
                    $file
                ),
                $this->compiler->text($rawContent)
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

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
use TechDivision\Markman\Utils\Template;


/**
 * TechDivision\Markman\Compiler
 *
 * Compiler class using Parsedown to create an exact html copy of the online markdown documentation and
 * its structure.
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
     * The actual compiler
     *
     * @var \Parsedown $compiler
     */
    protected $compiler;

    /**
     * Extensions of files we will parse and regenerate using our compiler
     *
     * @var array $allowedExtensions
     */
    protected $allowedExtensions;

    /**
     * Extensions of files we need to preserve and therefor copy to the compiled documentation.
     * Images for example.
     *
     * @var array $preservedExtensions
     */
    protected $preservedExtensions;

    /**
     * An instance of the configuration
     *
     * @var \TechDivision\Markman\Config $config
     */
    protected $config;

    /**
     * Default constructor
     *
     * @param \TechDivision\Markman\Config $config The project's configuration instance
     */
    public function __construct(Config $config)
    {
        // Save the configuration
        $this->config = $config;

        // Get ourselves an instance of the Parsedown compiler
        $this->compiler = new \Parsedown();
        $this->template = new Template($config->getValue(Config::PROJECT_NAME));

        // Prefill the allowed and preserved extensions
        $this->allowedExtensions = array_flip(array('md', 'markdown'));
        $this->preservedExtensions = array_flip(array('png', 'jpg', 'jpeg', 'svg', 'css', 'html', 'phtml'));
    }

    /**
     * Will compile the documentation file structure at the given tmp path.
     * Will generate the same file structure with turning markdown into html.
     * Will also generate navigational elements and embed the documentation content into the configured
     * template.
     *
     * @param string $tmpFilesPath   Path to the temporary, raw, documentation
     * @param string $targetBasePath Path to write the documentation to
     * @param array  $versions       Versions a documentation exists for
     * @param string $pathModifier   A certain part of the folder structure, like a base path we have to know
     *
     * @return bool
     */
    public function compile($tmpFilesPath, $targetBasePath, $versions, $pathModifier = '')
    {
        // Is there anything useful here?
        if (!is_readable($tmpFilesPath)) {

            return false;
        }

        // First of all we need a version file
        $this->compileVersionSwitch($versions);

        // Path prefix
        $pathPrefix = $this->config->getValue(Config::BUILD_PATH) . DIRECTORY_SEPARATOR .
            $targetBasePath . DIRECTORY_SEPARATOR;

        // Get an iterator over the part of the directory we want
        $iterator = $this->getDirectoryIterator($tmpFilesPath);

        // Compile all the files
        $fileUtil = new File();
        foreach ($iterator as $file) {

            // Get the content of the file
            $rawContent = file_get_contents($file);

            // Create the name of the target file
            $targetFile = str_replace($tmpFilesPath, '', $file);

            // If the file has a markdown extension we will compile it, if it is something we have to preserve we
            // will do so, if not we will skip it
            if (isset($this->allowedExtensions[$file->getExtension()])) {

                $rawContent = $this->compiler->text($rawContent);
                $targetFile = str_replace($file->getExtension(), 'html', $targetFile);

            } elseif (!isset($this->preservedExtensions[strtolower($file->getExtension())])) {

                continue;
            }

            // Apply any name mapping there might be
            if (count($this->config->getValue(Config::FILE_MAPPING)) > 0) {

                // Split up the mapping and apply it
                $haystacks = array_keys($this->config->getValue(Config::FILE_MAPPING));
                $targetFile = str_replace($haystacks, $this->config->getValue(Config::FILE_MAPPING), $targetFile);
            }

            $content =  $this->template->getTemplate(array('{content}'=> $rawContent));

            // Save the processed (or not) content to a file.
            // Recreate the path the file originally had
            $fileUtil->fileForceContents($pathPrefix . $targetFile,  $content);
        }

        // Now let's generate the navigation
        $this->generateNavigation($pathPrefix . $pathModifier, $pathPrefix);
    }

    /**
     * Will generate a separate file containing a html list of all versions a documentation has
     *
     * @param array $versions Array of versions
     *
     * @return void
     */
    public function compileVersionSwitch(array $versions)
    {
        // Build up the html
        $html = '<ul id="' . $this->config->getValue(Config::VERSION_SWITCHER_FILE_NAME) . '">';
        foreach ($versions as $version) {

            $html .= '<li node="' . $version->getName() . '">' . $version->getName() . '</li>';
        }
        $html .= '</ul>';

        // Write html to file
        $fileUtil = new File();
        $fileUtil->fileForceContents(
            $this->config->getValue(Config::BUILD_PATH) . DIRECTORY_SEPARATOR .
            $this->config->getValue(Config::PROJECT_NAME) . DIRECTORY_SEPARATOR .
            $this->config->getValue(Config::VERSION_SWITCHER_FILE_NAME) . '.html',
            $html
        );
    }

    /**
     * Will generate a navigation for a certain folder structure
     *
     * @param string $srcPath    Path to get the structure from
     * @param string $targetPath Path to write the result to
     *
     * @return void
     */
    protected function generateNavigation($srcPath, $targetPath)
    {
        // Write to file
        $fileUtil = new File();
        $fileUtil->fileForceContents(
            $targetPath . $this->config->getValue(Config::NAVIGATION_FILE_NAME) . '.html',
            '<ul id="navigation">' . $this->generateRecursiveList(new \DirectoryIterator($srcPath), '') . '</ul>'
        );
    }

    /**
     * Will recursively generate a html list based on a directory structure
     *
     * @param \DirectoryIterator $dir      The directory to add to the list
     * @param string             $nodePath The path of the nodes already collected
     *
     * @return string
     */
    protected function generateRecursiveList(\DirectoryIterator $dir, $nodePath)
    {
        $out = '';
        foreach ($dir as $node) {

            // Create the link path
            $linkPath = $this->config->getValue(Config::NAVIGATION_BASE) . $nodePath . $node;

            // If we got a directory we have to go deeper. If not we can add another link
            if ($node->isDir() && !$node->isDot()) {

                // Stack up the node path as we need for out links
                $nodePath .= $node . DIRECTORY_SEPARATOR;

                // If the directory contains an index file we will link it to the dir
                if (isset(array_flip(scandir($node->getRealPath()))['index.html'])) {

                    $nodeName = '<a href="' . $linkPath . DIRECTORY_SEPARATOR . 'index.html">' . $node . '</a>';

                } else {

                    $nodeName = $node;
                }

                // Make a recursion with the new path
                $out .= '<li node="' . $node . '">' . $nodeName . '<ul>' .
                    $this->generateRecursiveList(new \DirectoryIterator($node->getPathname()), $nodePath) .
                    '</ul></li>';

                // Clean the last path segment as we do need it within this loop
                $nodePath = str_replace($node . DIRECTORY_SEPARATOR, '', $nodePath);

            } elseif ($node->isFile()) {
                // A file is always a leaf, so it cannot be an ul element

                // We will skip index files as actual leaves
                if ($node == 'index.html') {

                    continue;
                }

                // Create the actual leaf
                $out .= '<li node="' . strstr($node, ".", true) . '"><a href="' .
                    $linkPath . '">' .
                    strstr($node, ".", true) . '</a></li>';
            }
        }

        return $out;
    }

    /**
     * Will Return an iterator over a set of files determined by a list of directories to iterate over
     *
     * @param string $paths List of directories to iterate over
     *
     * @return \Iterator
     */
    protected function getDirectoryIterator($paths)
    {
        // If we are no array, we have to make it one
        if (!is_array($paths)) {

            $paths = array($paths);
        }

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

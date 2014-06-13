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
use TechDivision\Markman\Utils\Parsedown;
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
        $this->template = new Template($config);

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
     *
     * @return bool
     */
    public function compile($tmpFilesPath, $targetBasePath, $versions)
    {
        // Is there anything useful here?
        if (!is_readable($tmpFilesPath)) {

            return false;
        }

        // First of all we need a version file
        $this->compileVersionSwitch($versions);

        // Path prefix which points into the generated folder with the specific version we are compiling right now
        $pathPrefix = $this->config->getValue(Config::BUILD_PATH) . DIRECTORY_SEPARATOR .
            $targetBasePath . DIRECTORY_SEPARATOR;

        // Now let's generate the navigation
        $this->generateNavigation($tmpFilesPath, $pathPrefix);

        // Added version switcher and navigation elements
        $this->template->getTemplate(
            array(
                '{version-switcher-element}' => file_get_contents(
                    $this->config->getValue(Config::BUILD_PATH) . DIRECTORY_SEPARATOR .
                    $this->config->getValue(Config::PROJECT_NAME) . DIRECTORY_SEPARATOR .
                    $this->config->getValue(Config::VERSION_SWITCHER_FILE_NAME) . '.html'
                ),
                '{navigation-element}' => file_get_contents(
                    $pathPrefix .
                    $this->config->getValue(Config::NAVIGATION_FILE_NAME) . '.html'
                )
            ),
            true
        );

        // Get an iterator over the part of the directory we want
        $iterator = $this->getDirectoryIterator($tmpFilesPath);

        // Compile all the files
        $fileUtil = new File();
        foreach ($iterator as $file) {

            // Get the content of the file
            $rawContent = file_get_contents($file);

            // Create the name of the target file relative to the containing base directory (tmp or build)
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

            // Create the html content. We will need the reverse path of the current file here
            $reversePath = $fileUtil->generateReversePath(
                substr($targetFile, 0, strrpos($targetFile, DIRECTORY_SEPARATOR))
            );
            // Now fill the template a last time and retrieve the complete template
            $content =  $this->template->getTemplate(
                array(
                    '{content}' => $rawContent,
                    '{relative-base-url}' => $reversePath . Template::VENDOR_DIR,
                    '{navigation-base}' => substr($reversePath, 0, strrpos($reversePath, DIRECTORY_SEPARATOR) + 1)
                )
            );

            // Clear the file specific changes of the template
            $this->template->clearTemplate();

            // Save the processed (or not) content to a file.
            // Recreate the path the file originally had
            $fileUtil->fileForceContents($pathPrefix . $targetFile, $content);
        }
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
            '<nav id="mp-menu" class="mp-menu">' .
            '<div id="' . $this->config->getValue(Config::NAVIGATION_FILE_NAME) .'" class="mp-level">
                <h2>' . $this->config->getValue(Config::PROJECT_NAME) . '</h2>
                <a class="mp-back" href="#">back</a>
                <ul>' . $this->generateRecursiveList(new \DirectoryIterator($srcPath), '') . '</ul>
            </div></nav>'
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
        // We will need a flipped file mapping as we work from the tmp dir
        $fileMapping = array_flip($this->config->getValue(Config::FILE_MAPPING));
        $mappedIndexFile = $fileMapping['index.html'];

        $out = '';
        $fileUtil = new File();
        $parsedownUtil = new Parsedown();
        foreach ($dir as $node) {

            // Create the link path
            $linkPath = str_replace('.md', '.html', $this->config->getValue(Config::NAVIGATION_BASE) . $nodePath . $node);

            // If we got a directory we have to go deeper. If not we can add another link
            if ($node->isDir() && !$node->isDot()) {

                // Stack up the node path as we need for out links
                $nodePath .= $node . DIRECTORY_SEPARATOR;

                // If the directory contains an index file we will link it to the dir
                if (isset(array_flip(scandir($node->getRealPath()))[$mappedIndexFile])) {

                    // In this case we have to include a link instead of a node name
                    $nodeName = '<a href="{navigation-base}' . $linkPath . DIRECTORY_SEPARATOR .
                        'index.html">' . $fileUtil->filenameToHeading($node) . '</a>';

                } else {

                    // Node name is just a beautified name
                    $nodeName = $fileUtil->filenameToHeading($node);
                }

                // Make a recursion with the new path
                $out .= '<li node="' . $node . '">' . $nodeName . '
                    <div class="mp-level">
                        <h2>' . $fileUtil->filenameToHeading($node) . '</h2>
                        <ul>' .
                    $this->generateRecursiveList(new \DirectoryIterator($node->getPathname()), $nodePath) .
                    '</ul></div></li>';

                // Clean the last path segment as we do need it within this loop
                $nodePath = str_replace($node . DIRECTORY_SEPARATOR, '', $nodePath);

            } elseif ($node->isFile()) {
                // A file is always a leaf, so it cannot be an ul element

                // We will skip index files as actual leaves
                if ($node == $mappedIndexFile) {

                    continue;
                }

                // Get the node's name (in a heading format)
                $nodeName = $fileUtil->filenameToHeading($node);

                // Do we have an markdown file? If so we will check for any markdown headings
                $headingBlock = '';
                if (isset($this->allowedExtensions[$node->getExtension()])) {

                    // We need the headings within the file
                    $headings = $parsedownUtil->getHeadings(
                        file_get_contents($dir->getPathname()),
                        $this->config->getValue(Config::NAVIGATION_HEADINGS_LEVEL)
                    );

                    // Create the list of headings as a ul/li list
                    $headingBlock = $this->generateHeadingBlock($headings, $nodeName);
                }

                // Create the actual leaf
                $out .= '<li node="' . strstr($node, ".", true) . '"><a href="{navigation-base}' .
                    $linkPath . '">' . $nodeName . '</a>' .
                    $headingBlock . '</li>';
            }
        }

        // Return the menu
        return $out;
    }

    /**
     * Will generate an html list for given headings
     *
     * @param array  $headings Headings to add to the block
     * @param string $nodeName Name of the node as a block heading
     *
     * @return string
     */
    protected function generateHeadingBlock(array $headings, $nodeName)
    {
        // We need a file util to create URL ready anchors
        $fileUtil = new File();

        // Iterate over the headings and build up a li list
        $html = '<div class="mp-level">
                        <h2>' . $nodeName . '</h2>
                        <ul>';
        foreach ($headings as $heading) {

            $html .= '<li class="heading"><a href="#' . $fileUtil->headingToFilename($heading) .
                '">' . $heading . '</a></li>';
        }

        return $html . '</ul></div>';
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

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
 * @subpackage Clients
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace TechDivision\Markman\Clients;

use TechDivision\Markman\Config;
use TechDivision\Markman\Loader;
use TechDivision\Markman\Compiler;
use TechDivision\Markman\Interfaces\ClientInterface;

/**
 * TechDivision\Markman\Clients\AbstractClient
 *
 * Provides an abstract base class all clients can use
 *
 * @category   Appserver
 * @package    TechDivision_Markman
 * @subpackage Clients
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
abstract class AbstractClient implements ClientInterface
{
    /**
     * An instance of the configuration
     *
     * @var \TechDivision\Markman\Config $config
     */
    protected $config;

    /**
     * The loader instance
     *
     * @var Loader $loader
     */
    protected $loader;

    /**
     * The compiler instance
     *
     * @var Compiler $compiler
     */
    protected $compiler;

    /**
     * Initialise the client
     *
     * @return void
     */
    public function init()
    {
        // Prepare the configuration if not done already
        if (!isset($this->config)) {

            $this->config = new Config();
        }

        // Get ourselves a loader and compiler
        $this->loader = new Loader($this->config);
        $this->compiler = new Compiler($this->config);
    }

    /**
     * This method will start the whole process of fetching the documentation and compiling it into a complete
     * and flat html documentation.
     *
     * @return void
     */
    public function run()
    {
        // Get the modifier for the path
        $pathModifier = 'docs';

        // Get all possible versions
        $versions = $this->loader->getVersions();

        // Iterate over all versions and get content
        $docs = array();
        foreach ($versions as $version) {

            // Get the docs
            $docs[$version->getName()] = $this->loader->getDocByVersion($version);
        }

        // Lets unpack the docs one by one and hand them to the compiler
        foreach ($docs as $version => $tmpFile) {

            // Collect what we need and hand it to the compiler
            $this->compiler->compile(
                $tmpFile . DIRECTORY_SEPARATOR . $this->loader->getSystemPathModifier($version),
                $this->config->getValue(Config::PROJECT_NAME) . DIRECTORY_SEPARATOR . $version,
                $versions,
                $pathModifier
            );
        }

        // Clear the tmp dir
        $this->clearTmpDirectory();
    }

    /**
     * Will clean the tmp directory by deleting all files within it
     *
     * @return void
     */
    protected function clearTmpDirectory()
    {
        // Clean the tmp dir
        foreach (scandir($this->config->getValue(Config::TMP_PATH)) as $tmpFile) {

            // Do not delete our .gitignore file
            if ($tmpFile === '.gitignore' || $tmpFile === '.') {

                continue;
            }

            // Delete the file
            unlink($this->config->getValue(Config::TMP_PATH) . DIRECTORY_SEPARATOR . $tmpFile);
        }
    }
}

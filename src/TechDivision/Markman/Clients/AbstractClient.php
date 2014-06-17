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
 * @category   Tools
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
use TechDivision\Markman\Utils\File;

/**
 * TechDivision\Markman\Clients\AbstractClient
 *
 * Provides an abstract base class all clients can use
 *
 * @category   Tools
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
     * Default constructor in which we check the time of the last run, if it was too recent we will not
     * do anything as a security precaution.
     */
    public function __construct()
    {
        // Get the mTime of the build dir
        $this->config = new Config();
        $mTime = filemtime($this->config->getValue(Config::BUILD_PATH));

        // If the mTime of the build dir is higher as NOW - the configured minimum we will fail
        if ((time() - $this->config->getValue(Config::MIN_TIME_INTERVAL) * 60) < $mTime) {

            throw new \Exception('Too recent usage of markman. Please be aware that we do not like flooding.');
        }
    }

    /**
     * Initialise the client
     *
     * @return void
     */
    public function init()
    {
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
                $tmpFile . DIRECTORY_SEPARATOR . $this->loader->getSystemPathModifier($version) . DIRECTORY_SEPARATOR .
                $this->config->getValue(Config::PATH_MODIFIER),
                $this->config->getValue(Config::PROJECT_NAME) . DIRECTORY_SEPARATOR,
                $version,
                $versions
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
        // Clean the tmp dir, we will need a file util to do so
        $fileUtil = new File();
        foreach (scandir($this->config->getValue(Config::TMP_PATH)) as $tmpFile) {

            // Do not delete our .gitignore file or parent directories!
            if ($tmpFile === '.gitignore' || $tmpFile === '.' || $tmpFile === '..') {

                continue;
            }

            // Delete the file
            $fileUtil->recursiveDirectoryDelete($this->config->getValue(Config::TMP_PATH) . DIRECTORY_SEPARATOR . $tmpFile);
        }
    }
}

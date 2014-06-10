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
 * @package    TechDivision
 * @subpackage Markman
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace TechDivision\Markman\Clients;

use TechDivision\Markman\Config;
use TechDivision\Markman\Loader;
use TechDivision\Markman\Compiler;
use TechDivision\Markman\Utils\Template;

// Let's get bootstrapped
require_once __DIR__ . DIRECTORY_SEPARATOR . 'bootstrapping.php';
/**
 * TechDivision\Markman\Clients\Cli
 *
 * Class to provide a simple command line interface
 *
 * @category   Tools
 * @package    TechDivision
 * @subpackage Markman
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
class Cli extends AbstractClient
{

    /**
     * Default constructor
     */
    public function __construct()
    {
        $name = 'appserver.io';
        $pathModifier = 'docs';

        // Prepare the configuration
        $config = new Config($name, 'github', 'techdivision/TechDivision_AppserverDocumentation');
        $config->setValue(Config::FILE_MAPPING, array('README' => 'index'));

        $this->loader   =  new Loader($config);
        $this->compiler =  new Compiler($config);
        $this->template =  new Template();
        $this->template->setTitle($name);


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
                $name . DIRECTORY_SEPARATOR . $version,
                $versions,
                $pathModifier
            );
        }

        $this->template->copyTemplateVendorDir(Config::BUILD_PATH. DIRECTORY_SEPARATOR .$name .
        DIRECTORY_SEPARATOR . "vendor"
        );

        // Clear the tmp dir
        $this->clearTmpDirectory();
    }
}
$cli = new Cli();

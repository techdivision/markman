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
class AbstractClient
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

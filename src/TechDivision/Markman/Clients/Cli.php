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
     * Arguments of files we will parse and regenerate using our compiler
     *
     * @var array $allowedExtensions
     */
    protected $allowedArguments;

    /**
     * Default constructor
     */
    public function __construct()
    {
        // Fill our allowed arguments and map them to config values
        $this->allowedArguments = array(
            'n' => Config::PROJECT_NAME,
            's' => Config::LOADER_HANDLER,
            'c' => Config::HANDLER_STRING,
            'm' => Config::PATH_MODIFIER
        );
    }

    /**
     * Will use a config instance to being used by markman based on the input the client gets
     *
     * @param mixed $args The arguments coming from the client's own input method
     *
     * @throws \Exception
     *
     * @return \TechDivision\Markman\Config
     */
    public function setConfigFromArgs($args)
    {
        // Get a config instance
        $config = new Config();

        // Iterate over all args and check if we can use them
        foreach ($args as $option => $value) {

            // If we do not know this option we will fail
            if (!isset($this->allowedArguments[$option])) {

                throw new \Exception('Unknown command line argument ' . $option);
            }

            // Set the value to the config
            $config->setValue($this->allowedArguments[$option], $value);
        }

        // Validate the configuration. We do not have to catch any exceptions, the calling script does already
        $config->validate();

        // Still here? Sounds good. Set the config and go back to the script
        $this->config = $config;
    }
}

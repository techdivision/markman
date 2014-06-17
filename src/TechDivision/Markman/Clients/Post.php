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

/**
 * TechDivision\Markman\Clients\Post
 *
 * Offers a client to invoke markman compilation by POST requests
 *
 * @category   Tools
 * @package    TechDivision_Markman
 * @subpackage Clients
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
class Post extends AbstractClient
{
    /**
     * Will use a config instance to being used by markman based on the input the client gets
     *
     * @param mixed $args The arguments coming from the client's own input method
     *
     * @throws \Exception
     *
     * @return \TechDivision\Markman\Config
     *
     * @TODO use different client handlers here!
     */
    public function setConfigFromArgs($args)
    {
        // If we do not get GitHub JSON we will fail
        if (!isset($args) || ($data = json_decode($args)) === null ||
            !isset($data->repository) || !isset($data->repository->full_name)) {

            throw new \Exception('Did not get any useful data within this POST request');
        }

        // Get a config instance
        $config = new Config();
        $config->setValue(Config::LOADER_HANDLER, 'github');
        $config->setValue(Config::HANDLER_STRING, $data->repository->full_name);

        // Validate the configuration. We do not have to catch any exceptions, the calling script does already
        $config->validate();

        // Still here? Sounds good. Set the config and go back to the script
        $this->config = $config;
    }
}

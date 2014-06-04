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

namespace TechDivision\Markman;

use TechDivision\Markman\Handler\GithubHandler;


/**
 * TechDivision\Markman\Loader
 *
 * Will load the a certain repository from github
 *
 * @category   Tools
 * @package    TechDivision
 * @subpackage Markman
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
class Loader
{
    /**
     * @var Handler\GithubHandler $handler <REPLACE WITH FIELD COMMENT>
     */
    protected $handler;

    /**
     * @param Config $config
     *
     * @throws \Exception
     */
    public function __construct(Config $config)
    {
        // Check if we got what we need
        if (strlen($config->getHandlerString()) === 0 || strlen($config->getSource()) === 0 ) {

            throw new \Exception('Missing critical loader configuration. Requires at least "source" and "handlerString".');
        }

        // Check if we got a usable handler
        switch ($config->getSource()) {

            case 'github':

                $this->handler = new GithubHandler();
                break;

            default:

                throw new \Exception('Missing handler for documentation source ' . $config->getSource());
        }

        // Connect the handler
        $this->handler->connect($config->getHandlerString());
    }

    /**
     *
     */
    public function getVersions()
    {
        return $this->handler->getVersions();
    }

    public function getDocByVersion($version)
    {
        return $this->handler->getDocByVersion($version);
    }

    public function getSystemPathModifier()
    {
        return $this->handler->getSystemPathModifier();
    }
}

 
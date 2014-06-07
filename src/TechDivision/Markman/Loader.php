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
use TechDivision\Markman\Interfaces\LoaderInterface;

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
class Loader implements LoaderInterface
{
    /**
     * The handler which will do the actual loader work
     *
     * @var \TechDivision\Markman\Interfaces\HandlerInterface $handler
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
        if (strlen($config->getValue(Config::HANDLER_STRING)) === 0 ||
            strlen($config->getValue(Config::LOADER_HANDLER)) === 0
        ) {

            throw new \Exception('Missing critical loader configuration. Requires at least "source" and "handlerString".');
        }

        // Check if we got a usable handler
        switch ($config->getValue(Config::LOADER_HANDLER)) {

            case 'github':

                $this->handler = new GithubHandler($config);
                break;

            default:

                throw new \Exception(
                    'Missing handler for documentation source ' .
                    $config->getValue(Config::LOADER_HANDLER)
                );
        }

        // Connect the handler
        $this->handler->connect($config->getValue(Config::HANDLER_STRING));
    }

    /**
     *
     */
    public function getVersions()
    {
        return $this->handler->getVersions();
    }

    /**
     * @param $version
     * @return string
     */
    public function getDocByVersion($version)
    {
        return $this->handler->getDocByVersion($version);
    }

    /**
     * @param $param
     * @return string
     */
    public function getSystemPathModifier($param)
    {
        return $this->handler->getSystemPathModifier($param);
    }
}

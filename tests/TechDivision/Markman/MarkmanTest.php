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
 * @category  Tools
 * @package   Markman
 * @author    Bernhard Wick <b.wick@techdivision.com>
 * @copyright 2014 TechDivision GmbH - <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php
 *            Open Software License (OSL 3.0)
 * @link      http://www.techdivision.com/
 */

namespace TechDivision\Markman;

/**
 * TechDivision\Markman\MarkmanTest
 *
 * Basic test class for the Markman library.
 *
 * @category  Tools
 * @package   Markman
 * @author    Bernhard Wick <b.wick@techdivision.com>
 * @copyright 2014 TechDivision GmbH - <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php
 *            Open Software License (OSL 3.0)
 * @link      http://www.techdivision.com/
 */
class MarkmanTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The config
     *
     * @var \TechDivision\Markman\Config
     */
    protected $config;

    /**
     * The loader
     *
     * @var \TechDivision\Markman\Loader
     */
    protected $loader;

    /**
     * Initializes what we need for our tests
     *
     * @return void
     */
    public function setUp()
    {
        // Get a config instance
        $this->config = new Config();
        $this->config->setValue(Config::LOADER_HANDLER, 'github');
        $this->config->setValue(Config::HANDLER_STRING, 'techdivision/TechDivision_AppserverDocumentation');
        // Get a loader instance
        $this->loader = new Loader($this->config);
    }

    /**
     * Test if the constructor created an instance of main classes.
     *
     * @return void
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf('\TechDivision\Markman\Config', $this->config);
        $this->assertInstanceOf('\TechDivision\Markman\Loader', $this->loader);
    }
}

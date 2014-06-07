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

/**
 * TechDivision\Markman\Config
 *
 * Class containing the configuration for the markman library. Might be filled from wherever.
 *
 * @category   Appserver
 * @package    TechDivision
 * @subpackage Markman
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
class Config
{
    /**
     * The variable containing all values
     *
     * @var array $values
     */
    protected $values;

    /**
     * Constants describing possible entries the config might have
     */
    const TMP_PATH = 'TMP_PATH';
    const BUILD_PATH = 'BUILD_PATH';
    const NAVIGATION_FILE_NAME = 'NAVIGATION_FILE_NAME';
    const VERSION_SWITCHER_FILE_NAME = 'VERSION_SWITCHER_FILE_NAME';
    const NAVIGATION_BASE = 'NAVIGATION_BASE';
    const PROJECT_NAME = 'PROJECT_NAME';
    const FILE_MAPPING = 'FILE_MAPPING';
    const LOADER_HANDLER = 'LOADER_HANDLER';
    const HANDLER_STRING = 'HANDLER_STRING';

    /**
     * Will preset some config values with reasonable values
     */
    public function __construct($projectName, $loaderHandler, $handleString)
    {
        // We at least need to know which dir to download to and build in
        $this->setValue(self::TMP_PATH, __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tmp');
        $this->setValue(self::BUILD_PATH, __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'build');

        // Also preset the names of version and navigation file
        $this->setValue(self::VERSION_SWITCHER_FILE_NAME, 'versions');
        $this->setValue(self::NAVIGATION_FILE_NAME, 'navigation');

        // Also essential, a base path for the navigation
        $this->setValue(self::NAVIGATION_BASE, '');

        // Lets also set the values passed as parameters
        $this->setValue(self::PROJECT_NAME, $projectName);
        $this->setValue(self::LOADER_HANDLER, $loaderHandler);
        $this->setValue(self::HANDLER_STRING, $handleString);
    }

    /**
     * Sets a value to specific content
     *
     * @param string $valueName The value to set
     * @param string $value     The actual content for the value
     *
     * @return void
     */
    public function setValue($valueName, $value)
    {
        if (!is_null($value)) {
            $this->values[$valueName] = $value;
        }
    }

    /**
     * Unsets a specific env var
     *
     * @param string $value The env var to unset
     *
     * @return void
     */
    public function unsetValue($value)
    {
        if (isset($this->values[$value])) {
            unset($this->values[$value]);
        }
    }

    /**
     * Return's a value for specific env var
     *
     * @param string $value The env var to get value for
     *
     * @throws \Exception
     *
     * @return mixed The value to given env var
     */
    public function getValue($value)
    {
        // check if server var is set
        if (isset($this->values[$value])) {
            // return server vars value
            return $this->values[$value];
        }
        // throw exception
        throw new \Exception("Config value '$value'' does not exist.");
    }

    /**
     * Returns all the values as array key value pair format
     *
     * @return array The values as array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Checks if value exists for given value
     *
     * @param string $value The value to check
     *
     * @return boolean Weather it has value (true) or not (false)
     */
    public function hasValue($value)
    {
        // check if server var is set
        if (!isset($this->values[$value])) {
            return false;
        }

        return true;
    }
}


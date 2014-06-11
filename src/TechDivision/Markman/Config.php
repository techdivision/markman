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
     * The elements which have to be present for a minimal configuration
     *
     * @var array $minimalConfiguration
     */
    protected $minimalConfiguration;

    /**
     * The default configuration file name.
     *
     * @const string DEFAULT_CONFIG_FILE
     */
    const DEFAULT_CONFIG_FILE = 'config.default.json';

    /**
     * Constants describing possible entries the config might have
     */
    const TMP_PATH = 'TMP_PATH';
    const BUILD_PATH = 'BUILD_PATH';
    const TEMPLATE_PATH = 'TEMPLATE_PATH';
    const NAVIGATION_FILE_NAME = 'NAVIGATION_FILE_NAME';
    const VERSION_SWITCHER_FILE_NAME = 'VERSION_SWITCHER_FILE_NAME';
    const NAVIGATION_BASE = 'NAVIGATION_BASE';
    const PROJECT_NAME = 'PROJECT_NAME';
    const FILE_MAPPING = 'FILE_MAPPING';
    const LOADER_HANDLER = 'LOADER_HANDLER';
    const HANDLER_STRING = 'HANDLER_STRING';
    const TEMPLATE_NAME = 'TEMPLATE_NAME';
    const MIN_TIME_INTERVAL = 'MIN_TIME_INTERVAL';
    const PATH_MODIFIER = 'PATH_MODIFIER';

    /**
     * Default constructor.
     * Will preset some config values with reasonable values
     *
     * @param string $configFilePath The path to the config file
     */
    public function __construct($configFilePath = null)
    {
        // Fill which things we need for a minimal configuration
        $this->minimalConfiguration = array(
            self::PROJECT_NAME,
            self::LOADER_HANDLER,
            self::HANDLER_STRING
        );

        // We have to load the configuration file. If there is none given we will load the default one
        if ($configFilePath === null) {

            $this->load(__DIR__ . DIRECTORY_SEPARATOR . self::DEFAULT_CONFIG_FILE, false);

        } else {

            $this->load($configFilePath);
        }

        // We at least need to know which dir to download to and build in
        $basePath = __DIR__ . DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
        $this->setValue(self::TMP_PATH, $basePath . 'tmp');
        $this->setValue(self::BUILD_PATH, $basePath . 'build');
        $this->setValue(self::TEMPLATE_PATH, $basePath . 'templates');

        // Also preset the names of version and navigation file
        $this->setValue(self::VERSION_SWITCHER_FILE_NAME, 'versions');
        $this->setValue(self::NAVIGATION_FILE_NAME, 'navigation');

        // Also essential, a base path for the navigation
        $this->setValue(self::NAVIGATION_BASE, '');

        // The time in between different compile cycles in minutes. Set as a precaution
        $this->setValue(self::MIN_TIME_INTERVAL, 5);
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

            // Do we know the value name? If not notice the user
            if (!defined('self::' . $valueName)) {

                // Create an error message but do not stop execution
                error_log('We do not seem to know the config value ' . $valueName . ' was that intentional?');
            }

            // Set the value nethertheless
            $this->values[$valueName] = $value;
        }
    }

    /**
     * Unsets a specific config value
     *
     * @param string $value The value to unset
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
     * Returns the content of a specific config value
     *
     * @param string $value The value to get the content for
     *
     * @throws \Exception
     *
     * @return mixed
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

    /**
     * Will load a certain configuration file into this instance. Might throw an exception if the file is not valid
     *
     * @param string  $file     The path of the configuration file we should load
     * @param boolean $validate Weather or not we will validate the config after reading the file
     *
     * @throws \Exception
     *
     * @return void
     */
    public function load($file, $validate = true)
    {
        // Check if we can read from the file
        if (!is_readable($file)) {

            throw new \Exception('Could not read from potential configuration file "' . $file . '".');
        }

        // Get the config
        $configCandidate = json_decode(file_get_contents($file));

        // Did we get the right thing?
        if (!$configCandidate instanceof \stdClass) {

            throw new \Exception('Configuration file does not contain valid JSON');
        }

        // Get the config using our mapping
        foreach ($configCandidate as $key => $configValue) {

            // Try to map the value
            $valueName = str_replace('-', '_', strtoupper($key));

            // If we got an array we have to make an explicit cast to kill the stdClass
            if (!is_string($configValue)) {

                $configValue = (array) $configValue;
            }

            // Set the value
            $this->setValue($valueName, $configValue);
        }

        // Done? Then validate if we are told to
        if ($validate) {

            $this->validate();
        }
    }

    /**
     * Will validate the configuration by looking if the minimal values have been set.
     *
     * @throws \Exception
     *
     * @return bool|void
     */
    public function validate()
    {
        // Iterate over all needed value names and check if we got entries for them
        foreach ($this->minimalConfiguration as $neededValue) {

            if (!$this->hasValue($neededValue)) {

                throw new \Exception('Missing needed configuration value ' . $neededValue);
            }
        }

        // Still here, sounds good
        return true;
    }
}

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
 * @subpackage Utils
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace TechDivision\Markman\Utils;

use TechDivision\Markman\Config;

/**
 * TechDivision\Markman\Utils\File
 *
 * File utility which provides additional file operation methods.
 *
 * @category   Appserver
 * @package    TechDivision_Markman
 * @subpackage Utils
 * @author     Lars Roettig <l.roettig@techdsivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
class Template
{
    /**
     * An instance of the configuration
     *
     * @var \TechDivision\Markman\Config $config
     */
    protected $config;

    /**
     * The template content
     *
     * @var string $baseTemplate
     */
    protected $baseTemplate;

    /**
     * The directory we keep CSS and JS libs in
     *
     * @const string VENDOR_DIR
     */
    const VENDOR_DIR = 'library';

    /**
     * The default configuration file name.
     *
     * @const string DEFAULT_CONFIG_FILE
     */
    const DEFAULT_TEMPLATE_NAME = 'default';

    /**
     * Default constructor
     *
     * @param \TechDivision\Markman\Config $config The project's configuration instance
     */
    public function __construct(Config $config)
    {
        // safe the config for later
        $this->config = $config;

        // Get the paths we need for our template engine. First of all check if we got a custom template
        $templateName = self::DEFAULT_TEMPLATE_NAME;
        if ($this->config->hasValue(Config::TEMPLATE_NAME)) {

            $templateName = $this->config->getValue(Config::TEMPLATE_NAME);
        }
        // Now we can create the paths we want :)
        $this->templateBasePath = $this->config->getValue(Config::TEMPLATE_PATH) . DIRECTORY_SEPARATOR .
            $templateName . DIRECTORY_SEPARATOR;
        $this->vendorBasePath = $this->templateBasePath . self::VENDOR_DIR;

        // Prepare the template
        $this->prepareTemplate();

        // Also copy CSS, JS ect. to the project dir
        $this->copyTemplateVendorDir();
    }

    /**
     * Getter for the title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->config->getValue(Config::PROJECT_NAME);
    }

    /**
     *
     */
    public function prepareTemplate()
    {
        $this->baseTemplate = file_get_contents($this->templateBasePath . 'index.html');

        $contentElements = array(
            '{head-title}' => $this->config->getValue(Config::PROJECT_NAME),
            '{project-name}' => $this->config->getValue(Config::PROJECT_NAME),
            '{base-url-css}' => self::VENDOR_DIR
        );

        $this->baseTemplate = $this->getTemplate($contentElements);
    }

    /**
     * 
     *
     * @param array $contentElements
     * @return mixed|string
     */
    public function getTemplate(array $contentElements)
    {
        $content = $this->baseTemplate;

        foreach ($contentElements as $contentElementKey => $contentElementValue) {
            $content = str_replace($contentElementKey, $contentElementValue, $content);
        }

        return $content;
    }

    /**
     *
     */
    protected function copyTemplateVendorDir()
    {
        // Get a file util class and recursively copy all the vendor stuff to the project dir
        $fileUtil = new File();
        $fileUtil->recursiveCopy(
            $this->vendorBasePath,
            $this->config->getValue(Config::BUILD_PATH) . DIRECTORY_SEPARATOR .
            $this->config->getValue(Config::PROJECT_NAME) . DIRECTORY_SEPARATOR .
            self::VENDOR_DIR
        );
    }
}

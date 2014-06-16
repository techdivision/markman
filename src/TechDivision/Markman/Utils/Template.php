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
 * @subpackage Utils
 * @author     Lars Roettig <l.roettig@techdsivision.com>
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
 * @category   Tools
 * @package    TechDivision_Markman
 * @subpackage Utils
 * @author     Lars Roettig <l.roettig@techdsivision.com>
 * @author     Bernhard Wick <b.wick@techdivision.com>
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
     * The template content after preparation
     *
     * @var string $baseTemplate
     */
    protected $baseTemplate;

    /**
     * The current template content containing file specific changes
     *
     * @var string $currentTemplate
     */
    protected $currentTemplate;

    /**
     * The base path of where the complete content will lie
     *
     * @var string $targetBasePath
     */
    protected $targetBasePath;

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
    const DEFAULT_TEMPLATE_NAME = 'appserver.io';

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
        $this->targetBasePath = $this->config->getValue(Config::BUILD_PATH) . DIRECTORY_SEPARATOR .
            $this->config->getValue(Config::PROJECT_NAME) . DIRECTORY_SEPARATOR;

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
     * Will prepare the template by including fixed elements into it.
     *
     * @return void
     */
    public function prepareTemplate()
    {
        // Get the templates base content
        $this->baseTemplate = file_get_contents($this->templateBasePath . 'index.html');

        // Collect content elements which do not change per file
        $contentElements = array(
                '{head-title}' => $this->config->getValue(Config::PROJECT_NAME),
                '{project-name}' => $this->config->getValue(Config::PROJECT_NAME)
        );

        // Render them in
        $this->baseTemplate = $this->getTemplate($contentElements);
    }

    /**
     * Will include passed content elements into the current template content.
     * Doing this several times allows for step-by-step template buildup
     *
     * @param array   $contentElements Elements which will be rendered into the template
     * @param boolean $persist         If you want the template changes to be persistent against clearTemplate()
     *
     * @return mixed|string
     */
    public function getTemplate(array $contentElements, $persist = false)
    {
        // Write to current template to preserve the baseTemplate
        $this->currentTemplate = $this->baseTemplate;

        // Include the elements into the template
        foreach ($contentElements as $contentElementKey => $contentElementValue) {
            $this->currentTemplate = str_replace($contentElementKey, $contentElementValue, $this->currentTemplate);
        }

        // If we have to persist our changes we have to write the current template back into the base one
        if ($persist) {

            $this->baseTemplate = $this->currentTemplate;
        }

        // Return the rendered template
        return $this->currentTemplate;
    }

    /**
     * Will clear the currently used template so there will be no overwriting of content
     *
     * @return void
     */
    public function clearTemplate()
    {
        $this->currentTemplate = null;
    }

    /**
     * Will copy template data into the build directory
     *
     * @return void
     */
    protected function copyTemplateVendorDir()
    {
        // Get a file util class and recursively copy all the vendor stuff to the project dir
        $fileUtil = new File();
        $fileUtil->recursiveCopy(
            $this->vendorBasePath,
            $this->targetBasePath . self::VENDOR_DIR
        );
    }
}

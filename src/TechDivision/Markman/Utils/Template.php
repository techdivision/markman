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

    private $templateName = 'default';


    private $templateBasePath = '/Users/roettigl/PhpstormProjects/markman/templates/default/index.html';

    private $vendorBasePath = '/Users/roettigl/PhpstormProjects/markman/templates/default/vendor';

    private $vendorUrl = 'http://localhost/markman/appserver.io/vendor';

    private $title = '';

    private $baseTemplate = '';

    public function  __construct($title = "")
    {
        $this->title = $title;
        $this->prepareTemplate();
    }


    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }


    public function prepareTemplate()
    {
        $this->baseTemplate = file_get_contents($this->templateBasePath);

        $contentElements = array('{head-title}' => $this->title,
            '{project-name}' => $this->title,
            '{base-url-css}' => $this->vendorUrl);

        $this->baseTemplate = $this->getTemplate($contentElements);

    }


    /**
     * @param $src
     * @param array $contentElements
     * @return mixed|string
     */
   public  function getTemplate(array $contentElements)
    {
        $content = $this->baseTemplate;

        foreach ($contentElements as $contentElementKey => $contentElementValue) {
            $content = str_replace($contentElementKey, $contentElementValue, $content);
        }

        return $content;
    }

    /**
     * @param $targetPath
     */
    public function copyTemplateVendorDir($targetPath)
    {
        $this->recursiveCopy($this->vendorBasePath, $targetPath);
    }


    protected function recursiveCopy($src, $dst)
    {

        // If source is not a directory stop processing
        if (!is_dir($src)) return false;


        echo  $dst;

        // If the destination directory does not exist create it
        if (!is_dir($dst)) {
            if (!mkdir($dst)) {
                // If the destination directory could not be created stop processing
                return false;
            }
        }

        // Open the source directory to read in files
        $i = new \DirectoryIterator($src);
        foreach ($i as $f) {
            if ($f->isFile()) {
                copy($f->getRealPath(), "$dst/" . $f->getFilename());
            } else if (!$f->isDot() && $f->isDir()) {
                $this->recursiveCopy($f->getRealPath(), "$dst/$f");
            }
        }
    }


}

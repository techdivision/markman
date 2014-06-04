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
 * <TODO CLASS DESCRIPTION>
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
     * @var  $fileMapping <REPLACE WITH FIELD COMMENT>
     */
    protected $fileMapping;

    /**
     * @var  $source <REPLACE WITH FIELD COMMENT>
     */
    protected $source;

    /**
     * @var  $handlerString <REPLACE WITH FIELD COMMENT>
     */
    protected $handlerString;

    /**
     * @param mixed $fileMapping
     */
    public function setFileMapping($fileMapping)
    {
        $this->fileMapping = $fileMapping;
    }

    /**
     * @return mixed
     */
    public function getFileMapping()
    {
        return $this->fileMapping;
    }

    /**
     * @param mixed $handlerString
     */
    public function setHandlerString($handlerString)
    {
        $this->handlerString = $handlerString;
    }

    /**
     * @return mixed
     */
    public function getHandlerString()
    {
        return $this->handlerString;
    }

    /**
     * @param mixed $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }
}

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
 * @subpackage Entities
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace TechDivision\Markman\Entities;

/**
 * TechDivision\Markman\Entities\Version
 *
 * <TODO CLASS DESCRIPTION>
 *
 * @category   Appserver
 * @package    TechDivision_Markman
 * @subpackage Entities
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
class Version
{
    /**
     * @var  $name <REPLACE WITH FIELD COMMENT>
     */
    protected $name;

    /**
     * @var  $download <REPLACE WITH FIELD COMMENT>
     */
    protected $download;

    /**
     * @param $name
     * @param $download
     */
    public function __construct($name, $download)
    {
        $this->name = $name;
        $this->download = $download;
    }

    /**
     * @return mixed
     */
    public function getDownload()
    {
        return $this->download;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}

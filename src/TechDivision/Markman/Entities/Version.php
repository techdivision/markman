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
 * Entity representing an immutable documentation version object
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
     * Version name, often the version number
     *
     * @var string $name
     */
    protected $name;

    /**
     * URL under which the documentation with this version resides
     *
     * @var string $download
     */
    protected $download;

    /**
     * Default constructor
     *
     * @param string $name     Version name, often the version number
     * @param string $download URL under which the documentation with this version resides
     */
    public function __construct($name, $download)
    {
        $this->name = $name;
        $this->download = $download;
    }

    /**
     * Getter for the $download member
     *
     * @return string
     */
    public function getDownload()
    {
        return $this->download;
    }

    /**
     * Getter for the $name member
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}

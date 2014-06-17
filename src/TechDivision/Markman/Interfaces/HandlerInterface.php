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
 * @subpackage Interfaces
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace TechDivision\Markman\Interfaces;

use TechDivision\Markman\Entities\Version;

/**
 * TechDivision\Markman\Interfaces\HandlerInterface
 *
 * Provides an interface all handler classes have to implement
 *
 * @category   Tools
 * @package    TechDivision_Markman
 * @subpackage Interfaces
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
interface HandlerInterface
{
    /**
     * Will return the different versions of a documentation
     *
     * @return array
     */
    public function getVersions();

    /**
     * Will download a certain version of a documentation and store it within the tmp directory.
     * Will return the path to the downloaded documentation.
     *
     * @param Version $version The version to download the documentation for
     *
     * @return string
     */
    public function getDocByVersion(Version $version);

    /**
     * Will return the system's path modifier, a certain path or name different documentation sources
     * will include in the
     *
     * @param string $param An additional parameter which can be included in the system's path modifier
     *
     * @return string
     */
    public function getSystemPathModifier($param);
}

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

/**
 * TechDivision\Markman\Interfaces\ClientInterface
 *
 * Interface describing
 *
 * @category   Tools
 * @package    TechDivision_Markman
 * @subpackage Interfaces
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
interface ClientInterface
{
    /**
     * Will use a config instance to being used by markman based on the input the client gets
     *
     * @param mixed $args The arguments coming from the client's own input method
     *
     * @throws \Exception
     *
     * @return \TechDivision\Markman\Config
     */
    public function setConfigFromArgs($args);
}

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
 * @subpackage Handler
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace TechDivision\Markman\Handler;

use TechDivision\Markman\Interfaces\HandlerInterface;

/**
 * TechDivision\Markman\Handler\AbstractHandler
 *
 * Abstract implementation of a handler which other handlers can inherit from
 *
 * @category   Tools
 * @package    TechDivision_Markman
 * @subpackage Handler
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
abstract class AbstractHandler implements HandlerInterface
{
    /**
     * The delimeter of any given handler string
     *
     * @const string HANDLE_STRING_DELIMETER
     */
    const HANDLE_STRING_DELIMETER = '/';
}

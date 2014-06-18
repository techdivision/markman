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
 * @subpackage Compilers
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace TechDivision\Markman\Compilers\Pre;

/**
 * \TechDivision\Markman\Compilers\Pre\GithubPreCompiler
 *
 * Is used to Pre-compile Github files, as they might not resolve to proper html
 *
 * @category   Tools
 * @package    TechDivision_Markman
 * @subpackage Compilers
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
class GithubPreCompiler
{
    /**
     * Will compile a Github style md by removing certain Github specialities
     *
     * @param string $md The text to compile
     *
     * @return string
     */
    public function compile($md)
    {
        return str_replace(array('(<', '>)'), array('(', ')'), $md);
    }
}

 
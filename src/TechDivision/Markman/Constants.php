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
 * TechDivision\Markman\Structure
 *
 * Will hold some basic constants which are used throughout the library
 *
 * @category   Appserver
 * @package    TechDivision
 * @subpackage Markman
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
class Constants
{
//@todo make this relative
    const TMP_PATH = '/Users/wickb/Workspace/markman/tmp';

    const BUILD_PATH = '/Users/wickb/Workspace/markman/build';

    const CURRENT_VERSION_NAME = 'current';

    const NAVIGATION_FILE_NAME = 'nav.html';

    const VERSION_SWITCHER_FILE_NAME = 'versions.html';

    const LINK_BASE_VARIABLE = '$navBaseUrl';
}

 
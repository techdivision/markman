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
 * TechDivision\Markman\Utils\Html
 *
 * <TODO CLASS DESCRIPTION>
 *
 * @category   Appserver
 * @package    TechDivision_Markman
 * @subpackage Utils
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
class Html
{


    protected function directories()
    {
        $directory = $_SERVER["PHP_SELF"];

        $directories = array();

        $next_slash = 0;

        do {//Creates an array with all the parent folders
            //of the file where this function is called.
            $next_slash = strpos($directory, "/", $next_slash);

            if($next_slash !== false)
            {
                $next_slash++;
                $directories[count($directories)] = substr($directory, 0, $next_slash);
            }

        } while($next_slash !== false);

        return($directories);
    }

//Uses the directories() function to create the links.
    protected function directoryNavigationLinks()
    {
        $directories = $this->directories();

        $links = "";

        for($index = 0; $index < count($directories); $index++)
        {
            //Handling the display is a little harder than the links themselves.
            $dir_name = substr($directories[$index], 0, strlen($directories[$index]) - 1);

            $dir_name = substr($dir_name, strrpos($dir_name, "/") + 1, strlen($dir_name) - 1);

            if($dir_name == "") $dir_name = "home";

            $links .= "<a href=\"" . $directories[$index] . "\">" . $dir_name . "</a>  ";
        }

        return($links);
    }
}

 
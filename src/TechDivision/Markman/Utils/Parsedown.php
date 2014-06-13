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
 * TechDivision\Markman\Utils\Parsedown
 *
 * Will allow to separately use Parsedown as a parser, not a compiler
 *
 * @category   Appserver
 * @package    TechDivision_Markman
 * @subpackage Utils
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
class Parsedown extends \Parsedown
{

    /**
     * Default constructor
     */
    public function __construct()
    {
        // Make sure no definitions are set
        $this->Definitions = array();
    }

    /**
     * Will return all headings of a markdown file. If specified will only return headings of a certain level.
     * There are the levels 1 to 3 (like html).
     *
     * @param string   $text  The text to parse
     * @param int|null $level The level to return the headings for
     *
     * @return array
     */
    public function getHeadings($text, $level = null)
    {
        // Standardize line breaks
        $text = str_replace("\r\n", "\n", $text);
        $text = str_replace("\r", "\n", $text);

        // Replace tabs with spaces
        $text = str_replace("\t", '    ', $text);

        // Remove surrounding line breaks
        $text = trim($text, "\n");

        // Split text into lines
        $lines = explode("\n", $text);

        // Iterate over all lines and check for headings
        $headings = array();
        foreach ($lines as $line) {

            if (is_array($tmp = $this->identifyAtx(array('text' => $line)))) {

                // Save the heading in a way it is sorted by level
                $headings[$tmp['element']['name']][] = $tmp['element']['text'];
            }
        }

        // If we got a level we have to filter for it
        if ($level !== null) {

            // Do we even have entries for this level?
            if (isset($headings['h' . $level])) {

                $headings = array($headings['h' . $level]);

            } else {

                return array();
            }
        }

        // Return a plain array without the level sub-array structure
        return array_shift($headings);
    }

}

 
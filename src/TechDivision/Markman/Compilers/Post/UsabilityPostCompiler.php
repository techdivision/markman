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

namespace TechDivision\Markman\Compilers\Post;

use TechDivision\Markman\Utils\File;
use TechDivision\Markman\Compilers\AbstractCompiler;
use TechDivision\Markman\Config;

/**
 * \TechDivision\Markman\Compilers\Post\UsabilityPostCompiler
 *
 * Is used for general post compilation steps which increase the usability of the documentation e.g. adding anchors.
 *
 * @category   Tools
 * @package    TechDivision_Markman
 * @subpackage Compilers
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
class UsabilityPostCompiler extends AbstractCompiler
{
    /**
     * Will compile the html and make changes to improve the usability
     *
     * @param string $html The text to compile
     *
     * @return string
     */
    public function compile($html)
    {
        // Add anchors into the headings
        $html = $this->addAnchors($html);

        return $html;
    }

    /**
     * Will process html code by adding anchors to the header elements
     *
     * @param string $html The html to process
     *
     * @return string
     */
    protected function addAnchors($html)
    {
        // We need a file util to get fine anchor names
        $fileUtil = new File();

        // Which headings level should we enhance with anchors?
        $headingsLevel = $this->config->getvalue(Config::NAVIGATION_HEADINGS_LEVEL);

        // Do the actual switching using regex
        $matches = array();
        preg_match_all('/<h' . $headingsLevel . '>(.+)<\/h' . $headingsLevel . '>/', $html, $matches);

        // No go over the second part of matches and build up the replacements for later
        foreach ($matches[1] as $key => $match) {

            $matches[1][$key] = '<h' . $headingsLevel . ' id="' . $fileUtil->headingToFilename($match) .
                '">' . $match . '</h' . $headingsLevel . '>';
        }

        // Now make the actual replacement
        return str_replace($matches[0], $matches[1], $html);
    }
}

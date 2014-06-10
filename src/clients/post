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
 * This file is used to invoke and control markman using a command line interface.
 * This file makes use of the \TechDivision\Markman\Clients\Cli class internally.
 *
 * @category   Tools
 * @package    TechDivision
 * @subpackage Markman
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

// Let's get our autoloader
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

// Get ourselves an instance of the Cli class
$post = new TechDivision\Markman\Clients\Post();

// Pass the request's POST data
try {

    // Try to generate a configuration from the request and set it to markman
    $post->setConfigFromArgs($_POST);

} catch (Exception $e) {

    // That did not work that well, return the error to the requesting party
    returnError($e->getMessage());

    // Exiting to prevent errors within markman
    exit;
}

// Init the client
$post->init();

// Now do the actual work
$post->run();

// Tell them we succeeded
echo "Ok";

/**
 * Will print a CLI formatted error message.
 *
 * @param string $message Error message
 */
function returnError($message)
{
    printf(json_decode('ERROR ' .$message));
}
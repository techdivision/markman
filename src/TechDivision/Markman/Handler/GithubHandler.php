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
 * @subpackage Handler
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace TechDivision\Markman\Handler;

use Github\Client;
use TechDivision\Markman\Config;
use TechDivision\Markman\Entities\Version;

/**
 * TechDivision\Markman\Handler\GithubHandler
 *
 * Handler for the Github API.
 *
 * @category   Appserver
 * @package    TechDivision_Markman
 * @subpackage Handler
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
class GithubHandler extends AbstractHandler
{
    /**
     * The client instance used to connect to the documentation source
     *
     * @var \Github\Client $client
     */
    protected $client;

    /**
     * Name of the user maintaining the documentation
     *
     * @var string $user
     */
    protected $user;

    /**
     * Name of the project within the Github
     *
     * @var string $project
     */
    protected $project;

    /**
     * A configuration instance
     *
     * @var \TechDivision\Markman\Config $config
     */
    protected $config;

    /**
     * Hostname of the documentation source
     *
     * @const string PROVIDER_HOST
     */
    const PROVIDER_HOST = 'https://github.com';

    /**
     * Name of the current documentation version name within the Github platform
     *
     * @const string CURRENT_VERSION_NAME
     */
    const CURRENT_VERSION_NAME = 'master';

    /**
     * Default constructor
     *
     * @param \TechDivision\Markman\Config $config A project configuration instance
     */
    public function __construct(Config $config)
    {
        // Safe the config for later use
        $this->config = $config;

        // Get the Github client
        $this->client = new Client();
    }

    /**
     * Will connect the handler to the documentation source
     *
     * @param string $handlerString A string containing needed connection information
     *
     * @return void
     */
    public function connect($handlerString)
    {
        // Split up the handler string
        list($this->user, $this->project) = explode(self::HANDLE_STRING_DELIMETER, $handlerString);
    }

    /**
     * Getter for the name the current version has within the Github platform
     *
     * @return string
     */
    public function getCurrentVersionName()
    {
        return self::CURRENT_VERSION_NAME;
    }

    /**
     * Will return the different versions of a documentation
     *
     * @return array
     */
    public function getVersions()
    {
        $rawVersions = $this->client->api('repo')->tags($this->user, $this->project);

        // First version of all is always current one
        $versions = array(
            new Version(
                self::CURRENT_VERSION_NAME,
                self::PROVIDER_HOST . '/' . $this->user . '/' . $this->project . '/archive/' . self::CURRENT_VERSION_NAME . '.zip'
            )
        );

        // Begin iteration
        foreach ($rawVersions as $rawVersion) {

            $versions[] = new Version(
                $rawVersion['name'],
                self::PROVIDER_HOST . '/' . $this->user . '/' . $this->project . '/archive/' . $rawVersion['name'] . '.zip'
            );
        }

        return $versions;
    }

    /**
     * Will download a certain version of a documentation and store it within the tmp directory.
     * Will return the path to the downloaded documentation.
     *
     * @param Version $version The version to download the documentation for
     *
     * @return string
     */
    public function getDocByVersion(Version $version)
    {

        // Save the tar in a tmp archive
        $tmpFile = md5($version->getDownload()) . '.zip';
        $targetDir = $this->config->getValue(Config::TMP_PATH) . DIRECTORY_SEPARATOR . $this->project .
            DIRECTORY_SEPARATOR . $version->getName();

        // Download the archive for the given version
        file_put_contents(
            $this->config->getValue(Config::TMP_PATH) . DIRECTORY_SEPARATOR . $tmpFile,
            file_get_contents($version->getDownload())
        );

        // Unpack em
        $data = new \ZipArchive();
        $data->open($this->config->getValue(Config::TMP_PATH) . DIRECTORY_SEPARATOR . $tmpFile);
        $data->extractTo($targetDir);

        // Unlink the tmp file, we don't need it any longer
        unlink($this->config->getValue(Config::TMP_PATH) . DIRECTORY_SEPARATOR . $tmpFile);

        return $targetDir;
    }

    /**
     * Will return the system's path modifier, a certain path or name different documentation sources
     * will include in the documentation structure.
     * Github always includes a string containing the project name and version separate by a "-" symbol.
     *
     * @param string $version The version of the documentation as we need to include it in the modifier
     *
     * @return string
     */
    public function getSystemPathModifier($version)
    {
        return $this->project . '-' . $version;
    }
}

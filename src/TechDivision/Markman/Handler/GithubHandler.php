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
use TechDivision\Markman\Constants;
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
     * @var \Github\Client $client <REPLACE WITH FIELD COMMENT>
     */
    protected $client;

    protected $user;

    protected $project;

    protected $branch = 'master';

    const PROVIDER_HOST = 'https://github.com';

    /**
     *
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param $handlerString
     */
    public function connect($handlerString)
    {
        // Split up the handler string
        list($this->user, $this->project) = explode(self::HANDLE_STRING_DELIMETER, $handlerString);
    }

    /**
     * @return array
     */
    public function getVersions()
    {
        $rawVersions = $this->client->api('repo')->tags($this->user, $this->project);

        // First version of all is always current one
        $versions = array(new Version(
            Constants::CURRENT_VERSION_NAME,
            self::PROVIDER_HOST . '/' . $this->user .  '/' . $this->project . '/archive/master.zip')
        );

        // Begin iteration
        foreach ($rawVersions as $rawVersion) {

            $versions[] = new Version($rawVersion['name'], $rawVersion['zipball_url']);
        }

        return $versions;
    }

    /**
     * @param Version $version
     * @return string
     * @throws \Exception
     */
    public function getDocByVersion(Version $version)
    {

        // Save the tar in a tmp archive
        $tmpFile = md5($version->getDownload()) . '.zip';
        $targetDir = Constants::TMP_PATH . DIRECTORY_SEPARATOR . $this->project .
            DIRECTORY_SEPARATOR . $version->getName();

        // Download the archive for the given version
        file_put_contents(
            Constants::TMP_PATH . DIRECTORY_SEPARATOR . $tmpFile,
            file_get_contents($version->getDownload())
        );

        // Unpack em
        try {
            $data = new \ZipArchive();
            $data->open(Constants::TMP_PATH . DIRECTORY_SEPARATOR . $tmpFile);
            $data->extractTo($targetDir);

        } catch (\Exception $e) {

            throw $e;
        }

        // Unlink the tmp file, we don't need it any longer
        unlink(Constants::TMP_PATH . DIRECTORY_SEPARATOR . $tmpFile);

        return $targetDir;
    }

    public function getSystemPathModifier()
    {
        return $this->project . '-' . $this->branch;
    }
}

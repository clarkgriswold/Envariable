<?php
/**
 * @copyright 2014
 */

namespace Envariable;

use Envariable\Helpers\EnvironmentHelper;
use Envariable\Helpers\ServerInterfaceHelper;

/**
 * Detect and Define the Environment.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class Environment
{
    /**
     * @var array
     */
    private $configMap;

    /**
     * @var \Envariable\Helpers\ServerInterfaceHelper
     */
    private $serverInterfaceHelper;

    /**
     * @var \Envariable\Helpers\EnvironmentHelper
     */
    private $environmentHelper;

    /**
     * @param array $configMap
     */
    public function __construct(array $configMap)
    {
        $this->configMap = $configMap;
    }

    /**
     * Define the Server Interface Helper
     *
     * @param \Envariable\Helpers\ServerInterfaceHelper $serverInterfaceHelper
     */
    public function setServerInterfaceHelper(ServerInterfaceHelper $serverInterfaceHelper)
    {
        $this->serverInterfaceHelper = $serverInterfaceHelper;
    }

    /**
     * Define the Environment Helper
     *
     * @param \Envariable\Helpers\EnvironmentHelper $environmentHelper
     */
    public function setEnvironmentHelper(EnvironmentHelper $environmentHelper)
    {
        $this->environmentHelper = $environmentHelper;
    }

    /**
     * Detect the environment and define the ENVIRONMENT constant.
     *
     * @throws \Exception
     */
    public function detect()
    {
        if ($this->serverInterfaceHelper->getType() === 'cli') {
            $this->environmentHelper->defineEnvironment($this->configMap['cliDefaultEnvironment']);
            $this->environmentHelper->verifyEnvironment();

            return;
        }

        if (empty($this->configMap['environmentToHostnameMap'])) {
            throw new \Exception('You have not defined any hostnames within the "environmentToHostnameMap" array within Envariable config.');
        }

try {
        $result = array_filter($this->configMap['environmentToHostnameMap'], array($this, 'isValidHostname'));
}catch (\Exception $e) {
    error_log($e->getMessage());
}
//error_log(var_export($result, 1));

        if ( ! empty($result)) {
            $this->environmentHelper->defineEnvironment(key($result));
        }

        $this->environmentHelper->verifyEnvironment();
    }

    /**
     * Validate hostname and, if $hostname is an array, validate subdomain as well.
     *
     * @param string|array $hostname
     *
     * @return boolean
     */
    private function isValidHostname($hostname)
    {
        if (is_array($hostname)) {
            $validHostname = $this->environmentHelper->getHostname() === $hostname['hostname'];

            return $validHostname && (strpos($_SERVER['SERVER_NAME'], $hostname['subdomain']) === 0);
        }

        if ($this->environmentHelper->getHostname() === $hostname) {
            return true;
        }

        return false;
    }
}

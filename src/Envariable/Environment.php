<?php

namespace Envariable;

use Envariable\Util\Server;

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
     * @var \Envariable\Util\Server
     */
    private $server;

    /**
     * @var string
     */
    private $environment;

    /**
     * Define the configuration.
     *
     * @param array $configMap
     */
    public function setConfiguration(array $configMap)
    {
        $this->configMap = $configMap;
    }

    /**
     * Define the Server Utility.
     *
     * @param \Envariable\Util\Server $server
     */
    public function setServer(Server $server)
    {
        $this->server = $server;
    }

    /**
     * Detect the environment and define the ENVIRONMENT constant.
     *
     * @throws \Exception
     */
    public function detect()
    {
        if (empty($this->configMap['cliDefaultEnvironment'])) {
            throw new \Exception('cliDefaultEnvironment must contain a value within Envariable config.');
        }

        if (empty($this->configMap['environmentToHostnameMap'])) {
            throw new \Exception('You have not defined any hostnames within the "environmentToHostnameMap" array within Envariable config.');
        }

        if ($this->server->getInterfaceType() === 'cli') {
            $this->environment = $this->configMap['cliDefaultEnvironment'];

            return;
        }

        $result = array_filter($this->configMap['environmentToHostnameMap'], array($this, 'isValidHostname'));

        if (empty($result)) {
            throw new \Exception('Could not detect the environment.');
        }

        $this->environment = key($result);
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
            $validHostname = $this->server->getHostname() === $hostname['hostname'];

            return $validHostname && (strpos($_SERVER['SERVER_NAME'], $hostname['subdomain']) === 0);
        }

        if ($this->server->getHostname() === $hostname) {
            return true;
        }

        return false;
    }

    /**
     * Retrieve the detected environment.
     *
     * @return string
     */
    public function getDetectedEnvironment()
    {
        return $this->environment;
    }
}

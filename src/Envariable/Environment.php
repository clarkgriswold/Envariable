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

        $result = array_filter($this->configMap['environmentToHostnameMap'], array($this, 'isValidEnvironment'));

        if (empty($result)) {
            throw new \Exception('Could not detect the environment.');
        }

        $this->environment = key($result);
    }

    /**
     * Validate given environment config data.
     *
     * @param string|array $configData
     *
     * @return boolean
     */
    private function isValidEnvironment($configData)
    {
        if (is_array($configData)) {
            $validationMethod = $this->determineValidationMethod($configData);

            $this->{$validationMethod}($configData);
        }

        if ($this->server->getHostname() === $configData) {
            return true;
        }

        return false;
    }

    /**
     * Determine the method of environment validation.
     *
     * @param array $configData
     *
     * @return boolean
     */
    private function determineValidationMethod(array $configData)
    {
        if (isset($configData['hostname']) && isset($configData['subdomain'])) {
            return 'validateHostnameAndSubdomain';
        }

        if (count($configData) === 1 && isset($configData['subdomain']))
        {
            return 'validateSubdomain';
        }
    }

    /**
     * Validate both hostname and subdomain.
     *
     * @param array $configData
     *
     * @return boolean
     */
    private function validateHostnameAndSubdomain(array $configData)
    {
        $validHostname = $this->server->getHostname() === $configData['hostname'];

        return $validHostname && $this->validateSubdomain($configData);
    }

    /**
     * Validate subdomain.
     *
     * @param array $configData
     *
     * @return boolean
     */
    private function validateSubdomain(array $configData)
    {
        return strpos($_SERVER['SERVER_NAME'], $configData['subdomain']) === 0;
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

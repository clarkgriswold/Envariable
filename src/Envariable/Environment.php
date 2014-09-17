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
     * @var array
     */
    private $environmentValidationStrategyMap;

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
     * Define the EnvironmentValidationStrategyMap.
     *
     * @param array $environmentValidationStrategyMap
     */
    public function setEnvironmentValidationStrategyMap(array $environmentValidationStrategyMap)
    {
        $this->environmentValidationStrategyMap = $environmentValidationStrategyMap;
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

        if (empty($result) || count($result) > 1) {
            throw new \Exception('Could not detect the environment.');
        }

        $this->environment = key($result);
    }

    /**
     * Validate given environment config data.
     *
     * @param array $configMap
     *
     * @return boolean
     */
    private function isValidEnvironment(array $configMap)
    {
        switch (true) {
            case isset($configMap['hostname']) && isset($configMap['subdomain']):
                $validationStrategy = $this->environmentValidationStrategyMap['HostnameSubdomainStrategy'];
                $validationStrategy->setServer($this->server);
                break;

            case count($configMap) === 1 && isset($configMap['hostname']):
                $validationStrategy = $this->environmentValidationStrategyMap['HostnameStrategy'];
                $validationStrategy->setServer($this->server);
                break;

            case count($configMap) === 1 && isset($configMap['subdomain']):
                $validationStrategy = $this->environmentValidationStrategyMap['SubdomainStrategy'];
        }

        return $validationStrategy->validate($configMap);
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

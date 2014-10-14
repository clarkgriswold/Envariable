<?php

namespace Envariable;

use Envariable\Util\Server;

/**
 * Environment Detector.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class EnvironmentDetector
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
     * @throws \RuntimeException
     */
    public function detect()
    {
        if (empty($this->configMap['cliDefaultEnvironment'])) {
            throw new \RuntimeException('cliDefaultEnvironment must contain a value within Envariable config.');
        }

        if (empty($this->configMap['environmentToDetectionMethodMap'])) {
            throw new \RuntimeException('You have not defined any hostnames within the "environmentToDetectionMethodMap" array within Envariable config.');
        }

        if ($this->server->getInterfaceType() === 'cli') {
            $this->environment = $this->configMap['cliDefaultEnvironment'];

            return;
        }

        $result = array_filter($this->configMap['environmentToDetectionMethodMap'], array($this, 'isValidEnvironment'));

        if (empty($result) || count($result) > 1) {
            throw new \RuntimeException('Could not detect the environment.');
        }

        $this->environment = key($result);
    }

    /**
     * Validate given environment config data.
     *
     * @param array $configMap
     *
     * @return boolean
     *
     * @throws \RuntimeException
     */
    private function isValidEnvironment(array $configMap)
    {
        switch (true) {
            case isset($configMap['hostname']) && isset($configMap['servername']):
                $validationStrategy = $this->environmentValidationStrategyMap['HostnameServernameStrategy'];
                $validationStrategy->setServer($this->server);
                break;

            case count($configMap) === 1 && isset($configMap['hostname']):
                $validationStrategy = $this->environmentValidationStrategyMap['HostnameStrategy'];
                $validationStrategy->setServer($this->server);
                break;

            case count($configMap) === 1 && isset($configMap['servername']):
                $validationStrategy = $this->environmentValidationStrategyMap['ServernameStrategy'];
                break;

            default:
                throw new \RuntimeException('Invalid hostname or servername configuration.');
        }

        return $validationStrategy->validate($configMap);
    }

    /**
     * Retrieve the detected environment.
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }
}

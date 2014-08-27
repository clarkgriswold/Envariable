<?php
/**
 * @copyright 2014
 */

namespace Envariable;

use Envariable\Util\ServerInterfaceHelper;

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
     * @var \Envariable\Util\ServerInterfaceHelper
     */
    private $serverAPIHelper;

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
     * @param \Envariable\Util\ServerInterfaceHelper $serverInterfaceHelper
     */
    public function setServerInterfaceHelper(ServerInterfaceHelper $serverInterfaceHelper)
    {
        $this->serverInterfaceHelper = $serverInterfaceHelper;
    }

    /**
     * Detect the environment and define the ENVIRONMENT constant.
     *
     * @return void
     */
    public function detect()
    {
        if ($this->serverInterfaceHelper->getType() === 'cli') {
            define('ENVIRONMENT', $this->configMap['cliDefaultEnvironment']);

            return;
        }

        if (count($this->configMap['environmentToHostMap']) === 0) {
            throw new \Exception('You have not defined any hosts within the "environmentToHostMap" array within Envariable config.');
        }

        foreach ($this->configMap['environmentToHostMap'] as $environment => $host) {
            if ( ! $this->isHostValid($host)) {
                continue;
            }

            define('ENVIRONMENT', $environment);
        }

        if ( ! defined('ENVIRONMENT')) {
            throw new \Exception('Could not detect the environment.');
        }
    }

    /**
     * Validate the host agains the server name.
     *
     * @param sting $host
     */
    private function isHostValid($host)
    {
        if ( ! isset($_SERVER['SERVER_NAME'])) {
            throw new \Exception('Server name is not defined.');
        }

        if ($_SERVER['SERVER_NAME'] !== $host) {
            return false;
        }

        return true;
    }
}

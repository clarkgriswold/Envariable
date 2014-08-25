<?php
/**
 * @copyright 2014
 */

namespace Envariable;

/**
 * Detect and Define the Environment.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class Environment
{
    /**
     * @param array $configMap
     */
    public function __construct(array $configMap)
    {
        $this->detect($configMap);
    }

    /**
     * Detect the environment and define the ENVIRONMENT constant.
     *
     * @return void
     */
    private function detect(array $configMap)
    {
        if (PHP_SAPI === 'cli') {
            define('ENVIRONMENT', $configMap['cliDefaultHost']);

            return;
        }

        if (count($configMap['environmentToHostMap']) === 0) {
            throw new \Exception('You have not defined any hosts within the "environmentToHostMap" array within Envariable config.');
        }

        foreach ($configMap['environmentToHostMap'] as $environment => $host) {
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

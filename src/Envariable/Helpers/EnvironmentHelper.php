<?php
/**
 * @copyright 2014
 */

namespace Envariable\Helpers;

/**
 * Helper Class To Define and Varify the ENVIRONMENT constant.
 */
class EnvironmentHelper
{
    /**
     * Define the ENVIRONMENT constant.
     *
     * @param string $environment
     */
    public function defineEnvironment($environment)
    {
        define('ENVIRONMENT', $environment);
    }

    /**
     * Verify that the ENVIRONMENT constant has been set.
     *
     * @throws \Exception
     */
    public function verifyEnvironment()
    {
        if ( ! defined('ENVIRONMENT')) {
            throw new \Exception('Could not detect the environment.');
        }
    }

    /**
     * Fetch the machine name.
     *
     * @return string
     */
    public function getHostname()
    {
        return gethostname();
    }
}

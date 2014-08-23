<?php
/**
 * @copyright 2014
 */

namespace Envariable;

/**
 *
 * @author Mark Kasaboski <markkasaboski@gmail.com>
 */
class Envariable
{
    /**
     * @var array
     */
    private $config;

    /**
     * ...
     */
    public function __construct()
    {
        $configFilePath = __DIR__ . '/config/config.php';

        if ( ! file_exists($configFilePath)) {
            throw new \Exception('Configuration file is required.');
        }

        $this->config = $this->requireConfigFile($configFilePath);

        $this->defineEnvironment();
        $this->defineCustomeEnvironmentConfigs();
    }

    /**
     * Define the environment constant.
     */
    private function defineEnvironment()
    {
        if (PHP_SAPI === 'cli') {
            define('ENVIRONMENT', $this->config['cliDefaultHost']);

            return;
        }

        foreach ($this->config['environmentToHostMap'] as $environment => $host) {
            if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] === $host) {
                define('ENVIRONMENT', $environment);
            }
        }

        if ( ! defined('ENVIRONMENT')) {
            throw new \Exception('Could not determine the environment');
        }
    }

    /**
     * Define
     */
    private function defineCustomeEnvironmentConfigs()
    {
        $applicationRootPath             = $this->getApplicationRootPath();
        $customEnvironmentConfigFilePath = sprintf('%s/.env.%s.php', $applicationRootPath, ENVIRONMENT);

        if ($this->config['customEnvironmentConfigPath'] !== null) {
            $customEnvironmentConfigPath     = rtrim($this->config['customEnvironmentConfigPath'], '/') . '/';
            $customEnvironmentConfigFilePath = sprintf('%s/%s.env.%s.php', $applicationRootPath, $customEnvironmentConfigPath, ENVIRONMENT);
        }

        $customEnvironmentConfigMap = $this->requireConfigFile($customEnvironmentConfigFilePath);

        $this->putCustomEnvironmentSettings($customEnvironmentConfigMap);
    }

    /**
     * Recurse over the user's custom environment config array and store the
     * settings within the environment variable store.
     *
     * @param array       $configMap
     * @param string|null $prefix
     */
    private function putCustomEnvironmentSettings(array $configMap, $prefix = null)
    {
        foreach ($configMap as $key => $value) {
            if (is_array($value)) {
                $this->putCustomEnvironmentSettings($value, $key);

                continue;
            }

            $setting = sprintf('%s_%s=%s', strtoupper($prefix), strtoupper($key), $value);

            putenv($setting);
        }
    }

    /**
     * Retrieve the application root path.
     *
     * @return string
     */
    private function getApplicationRootPath()
    {
        $backtrace = debug_backtrace();
        $backtrace = end($backtrace);

        return substr($backtrace['file'], 0, strrpos($backtrace['file'], '/'));
    }

    /**
     * Load a specified config file.
     *
     * @param string $configFilePath
     *
     * @return array
     */
    private function requireConfigFile($configFilePath)
    {
        return require($configFilePath);
    }
}

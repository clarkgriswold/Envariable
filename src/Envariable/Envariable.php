<?php
/**
 * @copyright 2014
 */

namespace Envariable;

use Envariable\Helpers\PathHelper;

/**
 * Put Custom Environment Settings Into the Environment Store.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class Envariable
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var string
     */
    private $environment;

    /**
     * @var \Envariable\Helpers\PathHelper
     */
    private $pathHelper;

    /**
     * Define the configuration.
     *
     * @param array $config
     */
    public function setConfiguration(array $config)
    {
        $this->config = $config;
    }

    /**
     * Define environment.
     *
     * @param string $environment
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    /**
     * Define the Path Helper
     *
     * @param \Envariable\Helpers\PathHelper $pathHelper
     */
    public function setPathHelper(PathHelper $pathHelper)
    {
        $this->pathHelper = $pathHelper;
    }

    /**
     * Execute the process of iterating over the custom config and
     * storing the data within environment variables.
     */
    public function putEnv()
    {
        $applicationRootPath             = $this->pathHelper->getApplicationRootPath();
        $customEnvironmentConfigFilePath = sprintf('%s/.env.%s.php', $applicationRootPath, $this->environment);

        if ($this->config['customEnvironmentConfigPath'] !== null) {
            $customEnvironmentConfigPath     = rtrim($this->config['customEnvironmentConfigPath'], '/') . '/';
            $customEnvironmentConfigFilePath = sprintf('%s/%s.env.%s.php', $applicationRootPath, $customEnvironmentConfigPath, $this->environment);
        }

        if ( ! file_exists($customEnvironmentConfigFilePath)) {
            throw new \Exception("Could not find configuration file: [$customEnvironmentConfigFilePath]");
        }

        $this->execute(require($customEnvironmentConfigFilePath));
    }

    /**
     * Recurse over the application's custom environment config array
     * and store the settings within the environment variable store.
     *
     * @param array       $configMap
     * @param string|null $prefix
     */
    private function execute(array $configMap, $prefix = null)
    {
        if (count($configMap) === 0) {
            throw new \RuntimeException('Your custom environment config is empty.');
        }

        array_walk($configMap, array($this, 'processConfigCallback'), $prefix);
    }

    /**
     * Process configuration entry and define as an environment variable.
     *
     * @param string|array $value
     * @param string       $key
     * @param string|null  $prefix
     */
    private function processConfigCallback($value, $key, $prefix = null)
    {
        if (is_array($value)) {
            $key = $prefix ? sprintf('%s_%s', $prefix, $key) : $key;

            $this->execute($value, $key);

            return;
        }

        $this->defineWithinENVSuperGlobal($prefix, $key, $value);
        $this->defineWithinEnvironmentStore($prefix, $key, $value);
    }

    /**
     * Define the custom environment variable within the $_ENV super global array.
     *
     * @param string $prefix
     * @param string $key
     * @param string $value
     */
    private function defineWithinENVSuperGlobal($prefix, $key, $value)
    {
        $key = sprintf('%s_%s', strtoupper($prefix), strtoupper($key));

        if (array_key_exists($key, $_ENV)) {
            throw new \Exception('An environment variable with the key "' . $key . '" already exists. Aborting.');
        }

        $_ENV[$key] = $value;
    }

    /**
     * Define the custom environment variable within the environment store.
     *
     * @param string $prefix
     * @param string $key
     * @param string $value
     */
    private function defineWithinEnvironmentStore($prefix, $key, $value)
    {
        $environmentVariableName = sprintf('%s_%s', strtoupper($prefix), strtoupper($key));

        if (getenv($environmentVariableName)) {
            throw new \Exception('An environment variable with the name "' . $environmentVariableName . '" already exists. Aborting.');
        }

        $setting = sprintf('%s=%s', $environmentVariableName, $value);

        putenv($setting);
    }
}

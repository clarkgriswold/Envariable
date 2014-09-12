<?php

namespace Envariable;

use Envariable\Util\Filesystem;

/**
 * Process the custom environment config and store them within
 * the $_ENV superglobal as well as the environment store (putenv).
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class CustomConfigProcessor
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
     * @var \Envariable\Util\Filesystem
     */
    private $filesystem;

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
     * Define the File System Utility.
     *
     * @param \Envariable\Util\Filesystem $filesystem
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Execute the process of iterating over the custom config and
     * storing the data within environment variables.
     *
     * @throws \Exception
     */
    public function execute()
    {
        $applicationRootPath             = $this->filesystem->getApplicationRootPath();
        $customEnvironmentConfigFilePath = sprintf('%s/.env.%s.php', $applicationRootPath, $this->environment);

        if ($this->config['customEnvironmentConfigPath'] !== null) {
            $customEnvironmentConfigPath     = rtrim($this->config['customEnvironmentConfigPath'], '/') . '/';
            $customEnvironmentConfigFilePath = sprintf('%s/%s.env.%s.php', $applicationRootPath, $customEnvironmentConfigPath, $this->environment);
        }

        if ( ! file_exists($customEnvironmentConfigFilePath)) {
            throw new \Exception("Could not find configuration file: [$customEnvironmentConfigFilePath]");
        }

        $this->process(require($customEnvironmentConfigFilePath));
    }

    /**
     * Iterate over the application's custom environment config array
     * and store the settings within the environment variable store.
     *
     * @param array       $configMap
     * @param string|null $prefix
     *
     * @throws \Exception
     */
    private function process(array $configMap, $prefix = null)
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

            $this->process($value, $key);

            return;
        }

        $this->defineWithinEnvSuperGlobal($prefix, $key, $value);
        $this->defineWithinEnvironmentStore($prefix, $key, $value);
    }

    /**
     * Define the custom environment variable within the $_ENV super global array.
     *
     * @param string $prefix
     * @param string $key
     * @param string $value
     *
     * @throws \Exception
     */
    private function defineWithinEnvSuperGlobal($prefix, $key, $value)
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
     *
     * @throws \Exception
     */
    private function defineWithinEnvironmentStore($prefix, $key, $value)
    {
        $environmentVariableName = sprintf('%s_%s', strtoupper($prefix), strtoupper($key));

        if (getenv($environmentVariableName)) {
            throw new \Exception('An environment variable with the name "' . $environmentVariableName . '" already exists. Aborting.');
        }

        putenv(sprintf('%s=%s', $environmentVariableName, $value));
    }
}

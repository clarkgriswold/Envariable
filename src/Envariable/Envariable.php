<?php
/**
 * @copyright 2014
 */

namespace Envariable;

use Envariable\Util\PathHelper;

/**
 * Put Custom Environment Settings Into the Environment Store.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class Envariable
{
    /**
     * @param array                       $config
     * @param \Envariable\Util\PathHelper $pathHelper
     */
    public function __construct(array $config, PathHelper $pathHelper = null)
    {
        $pathHelper                      = $pathHelper ?: new PathHelper();
        $applicationRootPath             = $pathHelper->getApplicationRootPath();
        $customEnvironmentConfigFilePath = sprintf('%s/.env.%s.php', $applicationRootPath, ENVIRONMENT);

        if ($config['customEnvironmentConfigPath'] !== null) {
            $customEnvironmentConfigPath     = rtrim($config['customEnvironmentConfigPath'], '/') . '/';
            $customEnvironmentConfigFilePath = sprintf('%s/%s.env.%s.php', $applicationRootPath, $customEnvironmentConfigPath, ENVIRONMENT);
        }

        $customEnvironmentConfigMap = require($customEnvironmentConfigFilePath);

        $this->putEnv($customEnvironmentConfigMap);
    }

    /**
     * Recurse over the application's custom environment config array
     * and store the settings within the environment variable store.
     *
     * @param array       $configMap
     * @param string|null $prefix
     */
    private function putEnv(array $configMap, $prefix = null)
    {
        if (count($configMap) === 0) {
            throw new \RuntimeException('Your custom environment config is empty.');
        }

        foreach ($configMap as $key => $value) {
            if (is_array($value)) {
                if ($prefix !== null) {
                    $key = sprintf('%s_%s', $prefix, $key);
                }

                $this->putEnv($value, $key);

                continue;
            }

            $this->defineWithinENVSuperGlobal($prefix, $key, $value);
            $this->defineWithinEnvironmentStore($prefix, $key, $value);
        }
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
            throw new \Exception('Array key "' . $key . '" already exists. Aborting.');
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

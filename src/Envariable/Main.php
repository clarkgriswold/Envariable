<?php

namespace Envariable;

class Main
{
    public function __construct()
    {
        $configFilePath = __DIR__ . '/config/config.php';

        if ( ! file_exists($configFilePath)) {
            throw new \Exception('Configuration file is required.');
        }

        $config = require($configFilePath);

        $this->defineEnvironment($config);
    }

    private function defineEnvironment(array $config)
    {
        if (defined('STDIN')) {
            define('ENVIRONMENT', $config['cliDefaultHost']);

            return;
        }

        foreach ($config['environmentToHostMap'] as $environment => $host) {
            $this->setEnvironment($environment, $host);
        }
    }

    private function setEnvironment($environment, $host)
    {
        if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] === $host) {
            define('ENVIRONMENT', $environment);

            return;
        }

        throw new \Exception('Could not determine the environment');
    }
}

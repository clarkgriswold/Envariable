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

        foreach ($config['hostList'] as $host) {
            echo $host, PHP_EOL;
        }
    }
}

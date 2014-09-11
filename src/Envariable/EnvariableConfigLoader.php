<?php

namespace Envariable;

/**
 * Envariable Config Loader.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class EnvariableConfigLoader
{
    /**
     * @var array
     */
    private $frameworkCommandList;

    public function __construct()
    {
        $frameworkCommandPath       = sprintf('%s%sConfig%sFrameworkCommand', __DIR__, DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR);
        $this->frameworkCommandList = glob($frameworkCommandPath . '/*.php');

        $key = array_search('FrameworkCommandInterface.php', $this->frameworkCommandList);

        unset($this->frameworkCommandList[$key]);
    }

    /**
     * Load the Envariable config file. If it
     * does not exist, create it, then load it.
     *
     * @return array
     */
    public function loadConfigFile()
    {
        foreach ($this->frameworkCommandList as $command) {
            $configFile = $command->loadConfigFile();

            if ( ! $configFile) {
                continue;
            }

            return $configFile;
        }
    }
}

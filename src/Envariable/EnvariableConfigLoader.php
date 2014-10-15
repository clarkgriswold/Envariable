<?php

namespace Envariable;

use Envariable\Config\FrameworkDetectionCommands\FrameworkDetectionCommandInterface;
use Envariable\Util\Filesystem;

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
    private $configMap = array();

    /**
     * @var array
     */
    private $frameworkDetectionCommandList = array();

    /**
     * Add a command to the framework command list.
     *
     * @param \Envariable\Config\FrameworkDetectionCommands\FrameworkDetectionCommandInterface $command
     */
    public function addCommand(FrameworkDetectionCommandInterface $command)
    {
        $this->frameworkDetectionCommandList[] = $command;
    }

    /**
     * Iterate over framework detection commands to identify an appropriate
     * Envariable config loader and then load the config.
     *
     * @return array
     *
     * @throws \RuntimeException
     */
    public function loadConfigFile()
    {
        try {
            array_walk($this->frameworkDetectionCommandList, array($this, 'loadConfigFileCallback'));
        } catch (\RuntimeException $runtimeException) {
            // Do nothing. Exiting array_walk as soon as a config has been loaded.
        }

        if ( ! count($this->configMap)) {
            throw new \RuntimeException('Could not load Envariable config.');
        }

        return $this->configMap;
    }

    /**
     * Callback to load config file from framework detection commands.
     *
     * @param \Envariable\Config\FrameworkDetectionCommands\FrameworkDetectionCommandInterface $command
     *
     * @throws \RuntimeException
     */
    private function loadConfigFileCallback(FrameworkDetectionCommandInterface $command)
    {
        $configMap = $command->loadConfigFile();

        if ($configMap) {
            $this->configMap = $configMap;

            throw new \RuntimeException('Early exit from array_walk...');
        }
    }
}

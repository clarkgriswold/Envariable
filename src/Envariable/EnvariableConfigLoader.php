<?php

namespace Envariable;

use Envariable\Config\FrameworkCommand\FrameworkCommandInterface;
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
    private $frameworkCommandList = array();

    /**
     * Add a command to the framework command list.
     *
     * @param \Envariable\Config\FrameworkCommand\FrameworkCommandInterface $command
     */
    public function addCommand(FrameworkCommandInterface $command)
    {
        $this->frameworkCommandList[] = $command;
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
            array_walk($this->frameworkCommandList, array($this, 'loadConfigFileCallback'));
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
     * @param \Envariable\Config\FrameworkCommand\FrameworkCommandInterface $command
     *
     * @throws \RuntimeException
     */
    private function loadConfigFileCallback(FrameworkCommandInterface $command)
    {
        $configMap = $command->loadConfigFile();

        if ($configMap) {
            $this->configMap = $configMap;

            throw new \RuntimeException('Early exit from array_walk...');
        }
    }
}

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
     * Load the Envariable config file. If it
     * does not exist, create it, then load it.
     *
     * @return array
     */
    public function loadConfigFile()
    {
        foreach ($this->frameworkCommandList as $command) {
            $configMap = $command->loadConfigFile();

            // Intentially breaking Object Calisthenics here until
            // I can find a better way of approaching this with the
            // Chain of Command (Chain of Responsibility) pattern.
            if ( ! $configMap) {
                continue;
            }

            return $configMap;
        }

        throw new \Exception('Could not load Envariable config.');
    }
}

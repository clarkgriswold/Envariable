<?php

namespace Envariable;

use Envariable\ConfigCreator;
use Envariable\FrameworkConfigPathLocatorCommands\FrameworkConfigPathLocatorCommandInterface;
use Envariable\Util\Filesystem;

/**
 * Envariable Config Loader.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class ConfigLoader
{
    /**
     * @var \Envariable\Util\Filesystem
     */
    private $filesystem;

    /**
     * @var \Envariable\ConfigCreator
     */
    private $configCreator;

    /**
     * @var string
     */
    private $configPath;

    /**
     * @var array
     */
    private $frameworkConfigPathLocatorCommandList = array();

    /**
     * Define the Filesystem utility.
     *
     * @param \Envaraible\Util\Filesystem $filesystem
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Define the ConfigCreator.
     *
     * @param \Envariable\ConfigCreator $configCreator
     */
    public function setConfigCreator(ConfigCreator $configCreator)
    {
        $this->configCreator = $configCreator;
    }

    /**
     * Add a command to the framework command list.
     *
     * @param \Envariable\FrameworkConfigPathLocatorCommands\FrameworkConigPathLocatorCommandInterface $command
     */
    public function addCommand(FrameworkConfigPathLocatorCommandInterface $command)
    {
        $this->frameworkConfigPathLocatorCommandList[] = $command;
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
            array_walk($this->frameworkConfigPathLocatorCommandList, array($this, 'frameworkConfigPathLocatorCallback'));
        } catch (\RuntimeException $runtimeException) {
            // Do nothing. Exiting array_walk as soon as a config has been loaded.
        }

        if ( ! $this->filesystem->fileExists($this->configPath)) {
            throw new \RuntimeException('Could not load Envariable config.');
        }

        $envariableConfigFilePath = sprintf('%s%sEnvariable%sconfig.php', $this->configPath, DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR);

        if ( ! $this->filesystem->fileExists($envariableConfigFilePath)) {
            $this->configCreator->createConfigFile($envariableConfigFilePath);
        }

        return $this->filesystem->loadConfigFile($envariableConfigFilePath);
    }

    /**
     * Framework config path locator callback.
     *
     * @param \Envariable\FrameworkConfigPathLocatorCommands\FrameworkConigPathLocatorCommandInterface $command
     *
     * @throws \RuntimeException
     */
    private function frameworkConfigPathLocatorCallback(FrameworkConfigPathLocatorCommandInterface $command)
    {
        $configPath = $command->locate();

        if ($configPath) {
            $this->configPath = $configPath;

            throw new \RuntimeException();
        }
    }
}

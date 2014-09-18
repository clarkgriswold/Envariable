<?php

namespace Envariable;

use Envariable\Util\Filesystem;

/**
 * Envariable Config Loader.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class EnvariableConfigLoader
{
    /**
     * @var \Envariable\Util\Filesystem
     */
    private $filesystem;

    /**
     * Define the Filesystem Utility.
     *
     * @param \Envariable\Util\Filesystem $filesystem
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Load the Envariable config file. If it
     * does not exist, create it, then load it.
     *
     * @return array
     */
    public function loadConfigFile()
    {
        $frameworkCommandList = $this->getFrameworkCommandList();

        foreach ($frameworkCommandList as $command) {
            $command->setFilesystem($this->filesystem);

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

    /**
     * Retrieve a list of all of the current framework commands.
     *
     * @return array
     */
    private function getFrameworkCommandList()
    {
        $frameworkCommandList = array();
        $frameworkCommandPath = sprintf('%s%sConfig%sFrameworkCommand', __DIR__, DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR);
        $commandFilePathList  = glob($frameworkCommandPath . '/*[!Interface].php');

        foreach ($commandFilePathList as $commandFilePath) {
            $commandNamespace = 'Envariable\\Config\\FrameworkCommand\\' . basename($commandFilePath, '.php');

            $frameworkCommandList[] = new $commandNamespace;
        }

        return $frameworkCommandList;
    }
}

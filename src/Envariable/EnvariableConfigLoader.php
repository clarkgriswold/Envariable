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
            $configMap = $command->loadConfigFile();

            if ( ! $configMap) {
                continue;
            }

            return $configMap;
        }

        throw new \Exception('Could not load Envariable config.');
    }

    private function getFrameworkCommandList()
    {
        $frameworkCommandList = array();
        $frameworkCommandPath = sprintf('%s%sConfig%sFrameworkCommand', __DIR__, DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR);
        $directoryIterator    = new \DirectoryIterator($frameworkCommandPath);

        foreach ($directoryIterator as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }

            $basename = $fileInfo->getBasename('.' . $fileInfo->getExtension());

            if (strpos($basename, 'Interface') !== false) {
                continue;
            }

            $namespace = 'Envariable\\Config\\FrameworkCommand\\' . $basename;

            $command = new $namespace;
            $command->setFilesystem($this->filesystem);

            $frameworkCommandList[] = $command;
        }

        return $frameworkCommandList;
    }
}

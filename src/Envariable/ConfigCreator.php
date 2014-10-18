<?php

namespace Envariable;

use Envariable\Util\Filesystem;

/**
 * Config Creator
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class ConfigCreator
{
    /**
     * @var \Envariable\Util\Filesystem
     */
    private $filesystem;

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
     * Copy the Envariable config template to the application config.
     *
     * @param string $applicationConfigFolderPath
     */
    public function createConfigFile($applicationConfigFolderPath)
    {
        $envariableConfigDirectoryPath = sprintf('%s%sEnvariable', $applicationConfigFolderPath, DIRECTORY_SEPARATOR);

        if ( ! $this->filesystem->createDirectory($envariableConfigDirectoryPath)) {
            throw new \Exception('Could not create Envariable config folder within application config folder.');
        }

        $envariableConfigFilePath = sprintf('%s%sEnvariable%sconfig.php', $applicationConfigFolderPath, DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR);

        if ( ! $this->filesystem->copyFile($this->getConfigTemplatePath, $envariableConfigFilePath)) {
            throw new \Exception('Could not copy config file to destination.');
        }
    }

    /**
     * Fetch the config template path.
     *
     * @return string
     */
    private function getConfigTemplatePath()
    {
        return sprintf('%s%sConfig%sConfigTemplate.php', __DIR__, DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR);
    }
}

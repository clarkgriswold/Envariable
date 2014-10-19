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
     * @param string $applicationConfigDirectoryPath
     */
    public function createConfigFile($applicationConfigDirectoryPath)
    {
        $envariableConfigDirectoryPath = sprintf('%s%sEnvariable', $applicationConfigDirectoryPath, DIRECTORY_SEPARATOR);

        if ( ! $this->filesystem->createDirectory($envariableConfigDirectoryPath)) {
            throw new \RuntimeException('Could not create Envariable config directory.');
        }

        $configTemplatePath       = sprintf('Config%sConfigTemplate.php', DIRECTORY_SEPARATOR);
        $envariableConfigFilePath = sprintf('%s%sconfig.php', $envariableConfigDirectoryPath, DIRECTORY_SEPARATOR);

        if ( ! $this->filesystem->copyFile($configTemplatePath, $envariableConfigFilePath)) {
            throw new \RuntimeException('Could not copy config file to destination.');
        }
    }
}

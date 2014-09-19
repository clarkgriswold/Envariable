<?php

namespace Envariable\Util;

/**
 * File System Utility.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class Filesystem
{

    /**
     * @var string
     */
    private $applicationRootPath;

    public function __construct()
    {
        $backtrace                 = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $backtrace                 = end($backtrace);
        $this->applicationRootPath = substr($backtrace['file'], 0, strrpos($backtrace['file'], '/'));
    }

    /**
     * Retrieve the application root path.
     *
     * @return string
     */
    public function getApplicationRootPath()
    {
        return $this->applicationRootPath;
    }

    /**
     * Retrieve the config file from the given config file path.
     *
     * @param string $configFilePath
     *
     * @return array
     */
    public function loadConfigFile($configFilePath)
    {
        return require($configFilePath);
    }

    /**
     * Create the Envariable config file within the app's config directory from the config template file.
     *
     * @param string $configTemplateFilePath
     * @param string $applicationConfigFolderPath
     */
    public function createConfigFile($configTemplateFilePath, $applicationConfigFolderPath)
    {
        $envariableConfigDirectoryPath = sprintf('%s%sEnvariable', $applicationConfigFolderPath, DIRECTORY_SEPARATOR);

        if ( ! mkdir($envariableConfigDirectoryPath, 0755)) {
            throw new \Exception('Could not create Envariable config folder within application config folder.');
        }

        $envariableConfigFilePath = sprintf('%s%sEnvariable%sconfig.php', $applicationConfigFolderPath, DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR);

        if ( ! copy($configTemplateFilePath, $envariableConfigFilePath)) {
            throw new \Exception('Could not copy config file to destination.');
        }
    }
}

<?php

namespace Envariable\Util;

/**
 * File System Utility.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class FileSystemUtil
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
    public function getConfigFile($configFilePath)
    {
        return require($configFilePath);
    }

    /**
     * Determine the path to the application folder.
     *
     * @return string
     */
    public function determineApplicationConfigFolderPath()
    {
        $rootDirectoryContentList = scandir($this->applicationRootPath);

        foreach ($rootDirectoryContentList as $element) {
            if (strpos($element, '.') === 0) {
                continue;
            }

            $configFolderPathCandidate      = sprintf('%s/%s/config', $this->applicationRootPath, $element);
            $controllersFolderPathCandidate = sprintf('%s/%s/controllers', $this->applicationRootPath, $element);

            if (file_exists($configFolderPathCandidate) && file_exists($controllersFolderPathCandidate)) {
                return $configFolderPathCandidate;
            }
        }

        throw new \Exception('Could not determine the path to the config folder.');
    }

    /**
     * Create the Envariable config file within the app's config directory from the config template file.
     *
     * @param string $configTemplateFilePath
     * @param string $applicationConfigFolderPath
     */
    public function createConfigFile($configTemplateFilePath, $applicationConfigFolderPath)
    {
        if ( ! mkdir($applicationConfigFolderPath . '/Envariable', 0755)) {
            throw new \Exception('Could not create Envariable config folder within application config folder.');
        }

        if ( ! copy($configTemplateFilePath, $applicationConfigFolderPath . '/Envariable/config.php')) {
            throw new \Exception('Could not copy config file to destination.');
        }
    }
}

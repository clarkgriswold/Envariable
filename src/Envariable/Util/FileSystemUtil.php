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
        $ds                                  = DIRECTORY_SEPARATOR;
        $rootDirectoryContentList            = scandir($this->applicationRootPath);
        $this->configFolderPathTemplate      = sprintf('%s%s%s%sconfig', $this->applicationRootPath, $ds, '%s', $ds);
        $this->controllersFolderPathTemplate = sprintf('%s%s%s%scontrollers', $this->applicationRootPath, $ds, '%s', $ds);

        $resultList = array_filter($rootDirectoryContentList, array($this, 'filterRootDirectoryContentListCallback'));

        if (empty($resultList) || count($resultList) > 1) {
            throw new \Exception('Could not determine the path to the config folder.');
        }

        return current($resultList);
    }

    /**
     * Filter root directory content list callback.
     *
     * @param string $element
     *
     * @return boolean
     */
    private function filterRootDirectoryContentListCallback($element) {
        if (strpos($element, '.') === 0) {
            return false;
        }

        $configFolderPathCandidate      = sprintf($this->configFolderPathTemplate, $element);
        $controllersFolderPathCandidate = sprintf($this->controllersFolderPathTemplate, $element);

        if (file_exists($configFolderPathCandidate) && file_exists($controllersFolderPathCandidate)) {
            return true;
        }
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

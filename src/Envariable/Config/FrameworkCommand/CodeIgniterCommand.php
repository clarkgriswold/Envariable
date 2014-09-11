<?php

namespace Envariable\Config\FrameworkCommand;

use Envariable\Config\FrameworkCommand\FrameworkCommandInterface;
use Envariable\Util\FileSystemUtil;

/**
 * Create the Envariable config within the CodeIgniter framework.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class CodeIgniterCommand implements FrameworkCommandInterface
{
    /**
     * @const Directory Separator
     */
    const DS = DIRECTORY_SEPARATOR;

    /**
     * @var \Envariable\Util\FileSystemUtil
     */
    private $filesystem;

    /**
     * @var string
     */
    private $applicationRootPath;

    /**
     * @var string|null
     */
    private $detectedConfigFolderPath;

    /**
     * Define the File System Utility.
     *
     * @param \Envaraible\Util\FileSystemUtil $filesystem
     */
    public function setFilesystem(FileSystemUtil $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function __construct()
    {
        $this->applicationRootPath      = $this->filesystem->getApplicationRootPath();
        $this->detectedConfigFolderPath = null;
    }

    /**
     * {@inheritdoc}
     */
    public function loadConfigFile()
    {
        $applicationConfigFolderPath = sprintf('%s%sapplication%sconfig', $this->applicationRootPath, self::DS, self::DS);

        if ( ! file_exists($applicationConfigFolderPath && ! $this->determineApplicationConfigFolderPath())) {
            return false;
        }

        $configFilePath = sprintf('%s%sEnvariable%sconfig.php', $detectedConfigFolderPath, self::DS, self::DS);

        if ( ! file_exists($configFilePath)) {
            $configTemplateFilePath = sprintf('%s%sConfig%sconfigTemplate.php', __DIR__, self::DS, self::DS);

            $this->filesystem->createConfigFile($configTemplateFilePath, $detectedConfigFolderPath);
        }

        return $this->filesystem->loadConfigFile($configFilePath);
    }

    /**
     * Determine the path to the application folder.
     *
     * @return string
     */
    private function determineApplicationConfigFolderPath()
    {
        $rootDirectoryContentList = scandir($this->applicationRootPath);

        $resultList = array_filter($rootDirectoryContentList, array($this, 'filterRootDirectoryContentListCallback'));

        if (empty($resultList) || count($resultList) > 1) {
            return false;
        }

        return true;
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

        $configFolderPathCandidate      = sprintf('%s%s%s%sconfig', $this->applicationRootPath, self::DS, $element, self::DS);
        $controllersFolderPathCandidate = sprintf('%s%s%s%scontrollers', $this->applicationRootPath, self::DS, $element, self::DS);

        if (file_exists($configFolderPathCandidate) && file_exists($controllersFolderPathCandidate)) {
            $this->detectedConfigFolderPath = $configFolderPathCandidate;

            return true;
        }
    }
}

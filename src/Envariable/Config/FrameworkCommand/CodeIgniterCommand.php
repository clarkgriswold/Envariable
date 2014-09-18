<?php

namespace Envariable\Config\FrameworkCommand;

use Envariable\Config\FrameworkCommand\FrameworkCommandInterface;
use Envariable\Util\Filesystem;

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
     * @var \Envariable\Util\Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $applicationRootPath;

    /**
     * @var string|null
     */
    private $applicationConfigFolderPath;

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
     * {@inheritdoc}
     */
    public function loadConfigFile()
    {
        $this->applicationRootPath         = $this->filesystem->getApplicationRootPath();
        $this->applicationConfigFolderPath = sprintf('%s%sapplication%sconfig', $this->applicationRootPath, self::DS, self::DS);

        if ( ! file_exists($this->applicationConfigFolderPath) && ! $this->determineApplicationConfigFolderPath()) {
            return false;
        }

        $configFilePath = sprintf('%s%sEnvariable%sconfig.php', $this->applicationConfigFolderPath, self::DS, self::DS);

        if ( ! file_exists($configFilePath)) {
            $configTemplateFilePath = sprintf('%s%sConfig%sconfigTemplate.php', __DIR__, self::DS, self::DS);

            $this->filesystem->createConfigFile($configTemplateFilePath, $this->applicationConfigFolderPath);
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
        $applicationRootDirectoryList = glob($this->applicationRootPath . '/*', GLOB_ONLYDIR);

        $resultList = array_filter($applicationRootDirectoryList, array($this, 'filterRootDirectoryContentListCallback'));

        if (empty($resultList) || count($resultList) > 1) {
            return false;
        }

        return true;
    }

    /**
     * Filter root directory content list callback.
     *
     * @param string $applicationRootPathCandidate
     *
     * @return boolean
     */
    private function filterRootDirectoryContentListCallback($applicationRootPathCandidate)
    {
        $configFolderPathCandidate      = sprintf('%s%sconfig', $applicationRootPathCandidate, self::DS);
        $controllersFolderPathCandidate = sprintf('%s%scontrollers', $applicationRootPathCandidate, self::DS);

        if (file_exists($configFolderPathCandidate) && file_exists($controllersFolderPathCandidate)) {
            $this->applicationConfigFolderPath = $configFolderPathCandidate;

            return true;
        }
    }
}

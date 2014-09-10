<?php
/**
 * @copyright 2014
 */

namespace Envariable;

use Envariable\ConfigurationProcessor;
use Envariable\Environment;
use Envariable\Util\ServerUtil;
use Envariable\Util\FileSystemUtil;

/**
 * Envariable Bootstrapper.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class Envariable
{
    /**
     * @var \Envariable\ConfigurationProcessor
     */
    private $ConfigurationProcessor;

    /**
     * @var \Envariable\Environment
     */
    private $environment;

    /**
     * @var \Envariable\Util\ServerUtil
     */
    private $serverUtil;

    /**
     * @var \Envariable\Util\FileSystemUtil
     */
    private $fileSystemUtil;

    /**
     * @param \Envariable\ConfigurationProcessor|null $ConfigurationProcessor
     * @param \Envariable\Environment|null            $environment
     * @param \Envariable\Util\ServerUtil|null        $serverUtil
     * @param \Envariable\Util\FileSystemUtil|null    $fileSystemUtil
     */
    public function __construct(
        ConfigurationProcessor $ConfigurationProcessor = null,
        Environment $environment = null,
        ServerUtil $serverUtil = null,
        FileSystemUtil $fileSystemUtil = null
    ) {
        $this->serverUtil             = $serverUtil ?: new ServerUtil();
        $this->environment            = $environment ?: new Environment();
        $this->ConfigurationProcessor = $ConfigurationProcessor ?: new ConfigurationProcessor();
        $this->fileSystemUtil         = $fileSystemUtil ?: new FileSystemUtil();
    }

    /**
     * Run Envariable.
     */
    private function execute()
    {
        $configMap = $this->getConfig();

        $this->initializeAndInvokeEnvironment($configMap);
        $this->initializeAndInvokeConfigurationProcessor($configMap);
    }

    /**
     * Retrieve the Envariable config file. Create it from the template
     * if it does not exist.
     *
     * @return array
     */
    private function getConfig()
    {
        $ds                          = DIRECTORY_SEPARATOR;
        $applicationRootPath         = $this->fileSystemUtil->getApplicationRootPath();
        $applicationConfigFolderPath = sprintf('%s%sapplication%sconfig', $applicationRootPath, $ds, $ds);

        if ( ! file_exists($applicationConfigFolderPath)) {
            $applicationConfigFolderPath = $this->fileSystemUtil->determineApplicationConfigFolderPath($applicationRootPath);
        }

        $configFilePath = sprintf('%s%sEnvariable%sconfig.php', $applicationConfigFolderPath, $ds, $ds);

        if ( ! file_exists($configFilePath)) {
            $configTemplateFilePath = sprintf('%s%sConfig%sconfig.php', __DIR__, $ds, $ds);

            $this->fileSystemUtil->createConfigFile($configTemplateFilePath, $applicationConfigFolderPath);
        }

        return $this->fileSystemUtil->getConfigFile($configFilePath);
    }

    /**
     * Initialize Environment and run it.
     *
     * @param array $configMap
     */
    private function initializeAndInvokeEnvironment(array $configMap)
    {
        $this->environment->setConfiguration($configMap);
        $this->environment->setServerUtil($this->serverUtil);

        $this->environment->detect();
    }

    /**
     * Initialize ConfigurationProcessor and run it.
     *
     * @param array $configMap
     */
    private function initializeAndInvokeConfigurationProcessor(array $configMap)
    {
        $this->ConfigurationProcessor->setConfiguration($configMap);
        $this->ConfigurationProcessor->setFileSystemUtil($this->fileSystemUtil);
        $this->ConfigurationProcessor->setEnvironment($this->environment->getDetectedEnvironment());

        $this->ConfigurationProcessor->execute();
    }

    /**
     * Retrieve the Environment instance.
     *
     * @return \Envariable\Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }
}

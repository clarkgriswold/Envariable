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
        Envariable $ConfigurationProcessor = null,
        Environment $environment = null,
        ServerUtil $serverUtil = null,
        FileSystemUtil $fileSystemUtil = null
    ) {
        $this->serverUtil             = $serverUtil ?: new ServerUtil();
        $this->environment            = $environment ?: new Environment();
        $this->ConfigurationProcessor = $ConfigurationProcessor ?: new ConfigurationProcessor();
        $this->fileSystemUtil         = $fileSystemUtil ?: new FileSystemUtil();

        $this->run();
    }

    /**
     * Run Envariable.
     */
    private function run()
    {
        $applicationRootPath         = $this->fileSystemUtil->getApplicationRootPath();
        $applicationConfigFolderPath = sprintf('%s%sapplication%sconfig', $applicationRootPath, DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR);

        if ( ! file_exists($applicationConfigFolderPath)) {
            $applicationConfigFolderPath = $this->fileSystemUtil->determineApplicationConfigFolderPath($applicationRootPath);
        }

        $configFilePath = sprintf('%s%sEnvariable%sconfig.php', $applicationConfigFolderPath, DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR);

        if ( ! file_exists($configFilePath)) {
            $configTemplateFilePath = __DIR__ . '/Config/config.php';

            $this->fileSystemUtil->createConfigFile($configTemplateFilePath, applicationConfigFolderPath);
        }

        $configMap = $this->fileSystemUtil->getConfigFile($configFilePath);

        $this->configureAndInovkeEnvironment($configMap);
        $this->configureAndInovkeEnvariable($configMap);
    }

    /**
     * Conigure Environment and run it.
     *
     * @param array $configMap
     */
    private function configureAndInovkeEnvironment(array $configMap)
    {
        $this->environment->setConfiguration($configMap);
        $this->environment->setServerUtil($this->serverUtil);

        $this->environment->detect();
    }

    /**
     * Configure Envariable and run it.
     *
     * @param array $configMap
     */
    private function configureAndInovkeEnvariable(array $configMap)
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

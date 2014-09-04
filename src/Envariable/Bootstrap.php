<?php
/**
 * @copyright 2014
 */

namespace Envariable;

use Envariable\Envariable;
use Envariable\Environment;
use Envariable\Util\EnvironmentUtil;
use Envariable\Util\ServerUtil;
use Envariable\Util\FileSystemUtil;

/**
 * Bootstrap Envariable.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class Bootstrap
{
    /**
     * @var \Envariable\Envariable
     */
    private $envariable;

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
     * @param \Envariable\Envariable|null           $envariable
     * @param \Envariable\Environment|null          $environment
     * @param \Envariable\Util\ServerUtil|null      $serverUtil
     * @param \Envariable\Util\FileSystemUtil|null  $fileSystemUtil
     */
    public function __construct(
        Envariable $envariable = null,
        Environment $environment = null,
        ServerUtil $serverUtil = null,
        FileSystemUtil $fileSystemUtil = null
    ) {
        $this->serverUtil      = $serverUtil ?: new ServerUtil();
        $this->environment     = $environment ?: new Environment();
        $this->envariable      = $envariable ?: new Envariable();
        $this->fileSystemUtil  = $fileSystemUtil ?: new FileSystemUtil();
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

        $environment = $this->environment->detect();

        $this->configureAndInovkeEnvariable($configMap, $environment);
    }

    /**
     * Configure Envariable and run it.
     *
     * @param array $configMap
     */
    private function configureAndInovkeEnvariable(array $configMap, $environment)
    {
        $this->envariable->setConfiguration($configMap);
        $this->envariable->setFileSystemUtil($this->fileSystemUtil);
        $this->envariable->setEnvironment($environment);

        $this->envariable->putEnv();
    }
}

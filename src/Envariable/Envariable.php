<?php

namespace Envariable;

use Envariable\CustomConfigProcessor;
use Envariable\EnvariableConfigLoader;
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
     * @var \Envariable\CustomConfigProcessor
     */
    private $customConfigProcessor;

    /**
     * @var \Envariable\EnvariableConfigLoader;
     */
    private $envariableConfigLoader;

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
     * @param \Envariable\CustomConfigProcessor|null  $customConfigProcessor
     * @param \Envariable\EnvariableConfigLoader|null $envariableConfigLoader
     * @param \Envariable\Environment|null            $environment
     * @param \Envariable\Util\ServerUtil|null        $serverUtil
     * @param \Envariable\Util\FileSystemUtil|null    $fileSystemUtil
     */
    public function __construct(
        CustomConfigProcessor $customConfigProcessor = null,
        EnvariableConfigLoader $envariableConfigLoader = null,
        Environment $environment = null,
        ServerUtil $serverUtil = null,
        FileSystemUtil $fileSystemUtil = null
    ) {
        $this->customConfigProcessor  = $customConfigProcessor ?: new CustomConfigProcessor();
        $this->envariableConfigLoader = $envariableConfigLoader ?: new EnvariableConfigLoader();
        $this->environment            = $environment ?: new Environment();
        $this->serverUtil             = $serverUtil ?: new ServerUtil();
        $this->fileSystemUtil         = $fileSystemUtil ?: new FileSystemUtil();
    }

    /**
     * Run Envariable.
     */
    public function execute()
    {
        $configMap = $this->envariableConfigLoader->loadConfigFile();
var_export($configMap);die;
        $this->initializeAndInvokeEnvironment($configMap);
        $this->initializeAndInvokeConfigurationProcessor($configMap);
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
     * Initialize CustomConfigProcessor and run it.
     *
     * @param array $configMap
     */
    private function initializeAndInvokeConfigurationProcessor(array $configMap)
    {
        $this->customConfigProcessor->setConfiguration($configMap);
        $this->customConfigProcessor->setFileSystemUtil($this->fileSystemUtil);
        $this->customConfigProcessor->setEnvironment($this->environment->getDetectedEnvironment());

        $this->customConfigProcessor->execute();
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

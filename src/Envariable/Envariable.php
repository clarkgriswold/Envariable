<?php

namespace Envariable;

use Envariable\Config\FrameworkDetectionCommand\CodeIgniterDetectionCommand;
use Envariable\Config\FrameworkDetectionCommand\FrameworkDetectionCommandInterface;
use Envariable\CustomConfigProcessor;
use Envariable\EnvariableConfigLoader;
use Envariable\EnvironmentDetector;
use Envariable\HostnameStrategy;
use Envariable\HostnameServernameStrategy;
use Envariable\ServernameStrategy;
use Envariable\Util\Filesystem;
use Envariable\Util\Server;

/**
 * Envariable Bootstrap.
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
     * @var \Envariable\EnvironmentDetector
     */
    private $environmentDetector;

    /**
     * @var \Envariable\Util\Server
     */
    private $server;

    /**
     * @var \Envariable\Util\Filesystem
     */
    private $filesystem;

    /**
     * @param \Envariable\CustomConfigProcessor|null  $customConfigProcessor
     * @param \Envariable\EnvariableConfigLoader|null $envariableConfigLoader
     * @param \Envariable\EnvironmentDetector|null    $environmentDetector
     * @param \Envariable\Util\Server|null            $server
     * @param \Envariable\Util\Filesystem|null        $filesystem
     */
    public function __construct(
        CustomConfigProcessor $customConfigProcessor = null,
        EnvariableConfigLoader $envariableConfigLoader = null,
        EnvironmentDetector $environmentDetector = null,
        Server $server = null,
        Filesystem $filesystem = null
    ) {
        $this->customConfigProcessor  = $customConfigProcessor ?: new CustomConfigProcessor();
        $this->envariableConfigLoader = $envariableConfigLoader ?: new EnvariableConfigLoader();
        $this->environmentDetector    = $environmentDetector ?: new EnvironmentDetector();
        $this->server                 = $server ?: new Server();
        $this->filesystem             = $filesystem ?: new Filesystem();

        $frameworkDetectionCommandList = array(
            new CodeIgniterDetectionCommand(),
            // Add more commands here...
        );

        array_map(array($this, 'addCommandCallback'), $frameworkDetectionCommandList);
    }

    /**
     * Add command callback.
     *
     * @param \Envariable\Config\FrameworkDetectionCommand\FrameworkDetectionCommandInterface $command
     */
    private function addCommandCallback(FrameworkDetectionCommandInterface $frameworkDetectionCommand)
    {
        $frameworkDetectionCommand->setFilesystem($this->filesystem);

        $this->envariableConfigLoader->addCommand($frameworkDetectionCommand);
    }

    /**
     * Run Envariable.
     */
    public function execute()
    {
        $configMap = $this->envariableConfigLoader->loadConfigFile();

        $this->initializeAndInvokeEnvironmentDetector($configMap);
        $this->initializeAndInvokeConfigurationProcessor($configMap);
    }

    /**
     * Initialize EnvironmentDetector and run it.
     *
     * @param array $configMap
     */
    private function initializeAndInvokeEnvironmentDetector(array $configMap)
    {
        $setEnvironmentValidationStrategyMap = array(
            'HostnameStrategy'           => new HostnameStrategy(),
            'HostnameServernameStrategy' => new HostnameServernameStrategy(),
            'ServernameStrategy'         => new ServernameStrategy(),
        );

        $this->environmentDetector->setConfiguration($configMap);
        $this->environmentDetector->setServer($this->server);
        $this->environmentDetector->setEnvironmentValidationStrategyMap($setEnvironmentValidationStrategyMap);

        $this->environmentDetector->detect();
    }

    /**
     * Initialize CustomConfigProcessor and run it.
     *
     * @param array $configMap
     */
    private function initializeAndInvokeConfigurationProcessor(array $configMap)
    {
        $this->customConfigProcessor->setConfiguration($configMap);
        $this->customConfigProcessor->setFilesystem($this->filesystem);
        $this->customConfigProcessor->setEnvironment($this->environmentDetector->getEnvironment());

        $this->customConfigProcessor->execute();
    }

    /**
     * Retrieve the EnvironmentDetector instance.
     *
     * @return \Envariable\EnvironmentDetector
     */
    public function getEnvironmentDetector()
    {
        return $this->environmentDetector;
    }
}

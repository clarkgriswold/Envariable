<?php

namespace Envariable;

use Envariable\Config\FrameworkDetectionCommands\CodeIgniterDetectionCommand;
use Envariable\Config\FrameworkDetectionCommands\FrameworkDetectionCommandInterface;
use Envariable\DotEnvConfigProcessor;
use Envariable\ConfigLoader;
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
     * @var \Envariable\DotEnvConfigProcessor
     */
    private $dotEnvConfigProcessor;

    /**
     * @var \Envariable\ConfigLoader;
     */
    private $configLoader;

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
     * @param \Envariable\DotEnvConfigProcessor|null $dotEnvConfigProcessor
     * @param \Envariable\ConfigLoader|null          $configLoader
     * @param \Envariable\EnvironmentDetector|null   $environmentDetector
     * @param \Envariable\Util\Server|null           $server
     * @param \Envariable\Util\Filesystem|null       $filesystem
     */
    public function __construct(
        DotEnvConfigProcessor $dotEnvConfigProcessor = null,
        ConfigLoader $configLoader = null,
        EnvironmentDetector $environmentDetector = null,
        Server $server = null,
        Filesystem $filesystem = null
    ) {
        $this->dotEnvConfigProcessor  = $dotEnvConfigProcessor ?: new DotEnvConfigProcessor();
        $this->configLoader           = $configLoader ?: new ConfigLoader();
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
     * @param \Envariable\Config\FrameworkDetectionCommands\FrameworkDetectionCommandInterface $command
     */
    private function addCommandCallback(FrameworkDetectionCommandInterface $frameworkDetectionCommand)
    {
        $frameworkDetectionCommand->setFilesystem($this->filesystem);

        $this->configLoader->addCommand($frameworkDetectionCommand);
    }

    /**
     * Run Envariable.
     */
    public function execute()
    {
        $configMap = $this->configLoader->loadConfigFile();

        $this->initializeAndInvokeEnvironmentDetector($configMap);
        $this->initializeAndInvokeDotEnvConfigProcessor($configMap);
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
     * Initialize DotEnvConfigProcessor and run it.
     *
     * @param array $configMap
     */
    private function initializeAndInvokeDotEnvConfigProcessor(array $configMap)
    {
        $this->dotEnvConfigProcessor->setConfiguration($configMap);
        $this->dotEnvConfigProcessor->setFilesystem($this->filesystem);
        $this->dotEnvConfigProcessor->setEnvironment($this->environmentDetector->getEnvironment());

        $this->dotEnvConfigProcessor->execute();
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

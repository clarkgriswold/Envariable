<?php

namespace Envariable;

use Envariable\ConfigCreator;
use Envariable\ConfigLoader;
use Envariable\DotEnvConfigProcessor;
use Envariable\EnvironmentDetector;
use Envariable\FrameworkConfigPathLocatorCommands\CodeIgniterConfigPathLocatorCommand;
use Envariable\FrameworkConfigPathLocatorCommands\FrameworkConfigPathLocatorCommandInterface;
use Envariable\HostnameServernameStrategy;
use Envariable\HostnameStrategy;
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
        $this->dotEnvConfigProcessor = $dotEnvConfigProcessor ?: new DotEnvConfigProcessor();
        $this->configLoader          = $configLoader ?: new ConfigLoader();
        $this->environmentDetector   = $environmentDetector ?: new EnvironmentDetector();
        $this->server                = $server ?: new Server();
        $this->filesystem            = $filesystem ?: new Filesystem();

        $configCreator = new ConfigCreator();

        $configCreator->setFilesystem($this->filesystem);
        $this->configLoader->setFilesystem($this->filesystem);
        $this->configLoader->setConfigCreator($configCreator);

        $frameworkConfigPathLocatorCommandList = array(
            new CodeIgniterConfigPathLocatorCommand(),
            // Add more commands here...
        );

        foreach ($frameworkConfigPathLocatorCommandList as $command) {
            $this->configLoader->addCommand($command);
        }
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

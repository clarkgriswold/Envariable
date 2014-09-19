<?php

namespace Envariable;

use Envariable\Config\FrameworkCommand\CodeIgniterCommand;
use Envariable\Config\FrameworkCommand\FrameworkCommandInterface;
use Envariable\CustomConfigProcessor;
use Envariable\EnvariableConfigLoader;
use Envariable\Environment;
use Envariable\HostnameStrategy;
use Envariable\HostnameSubdomainStrategy;
use Envariable\SubdomainStrategy;
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
     * @var \Envariable\Environment
     */
    private $environment;

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
     * @param \Envariable\Environment|null            $environment
     * @param \Envariable\Util\Server|null            $server
     * @param \Envariable\Util\Filesystem|null        $filesystem
     */
    public function __construct(
        CustomConfigProcessor $customConfigProcessor = null,
        EnvariableConfigLoader $envariableConfigLoader = null,
        Environment $environment = null,
        Server $server = null,
        Filesystem $filesystem = null
    ) {
        $this->customConfigProcessor  = $customConfigProcessor ?: new CustomConfigProcessor();
        $this->envariableConfigLoader = $envariableConfigLoader ?: new EnvariableConfigLoader();
        $this->environment            = $environment ?: new Environment();
        $this->server                 = $server ?: new Server();
        $this->filesystem             = $filesystem ?: new Filesystem();

        $frameworkCommandList = array(
            new CodeIgniterCommand(),
            // Add more commands here...
        );

        array_map(array($this, 'addCommandCallback'), $frameworkCommandList);
    }

    /**
     * Add command callback.
     *
     * @param \Envariable\Config\FrameworkCommand\FrameworkCommandInterface $command
     */
    private function addCommandCallback(FrameworkCommandInterface $frameworkCommand)
    {
        $frameworkCommand->setFilesystem($this->filesystem);

        $this->envariableConfigLoader->addCommand($frameworkCommand);
    }

    /**
     * Run Envariable.
     */
    public function execute()
    {
        $configMap = $this->envariableConfigLoader->loadConfigFile();

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
        $setEnvironmentValidationStrategyMap = array(
            'HostnameStrategy'          => new HostnameStrategy(),
            'HostnameSubdomainStrategy' => new HostnameSubdomainStrategy(),
            'SubdomainStrategy'         => new SubdomainStrategy(),
        );

        $this->environment->setConfiguration($configMap);
        $this->environment->setServer($this->server);
        $this->environment->setEnvironmentValidationStrategyMap($setEnvironmentValidationStrategyMap);

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
        $this->customConfigProcessor->setFilesystem($this->filesystem);
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

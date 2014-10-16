<?php

namespace spec\Envariable;

use Envariable\DotEnvConfigProcessor;
use Envariable\EnvariableConfigLoader;
use Envariable\EnvironmentDetector;
use Envariable\Util\Server;
use Envariable\Util\Filesystem;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Envariable Test.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class EnvariableSpec extends ObjectBehavior
{
    /**
     * @var \Envariable\EnvariableConfigLoader
     */
    private $envariableConfigLoader;

    /**
     * Pre-test setup.
     *
     * @param \Envariable\DotEnvConfigProcessor  $dotEnvConfigProcessor
     * @param \Envariable\EnvariableConfigLoader $envariableConfigLoader
     * @param \Envariable\EnvironmentDetector    $environmentDetector
     * @param \Envariable\Util\Server            $server
     * @param \Envariable\Util\Filesystem        $filesystem
     */
    function let(
        DotEnvConfigProcessor $dotEnvConfigProcessor,
        EnvariableConfigLoader $envariableConfigLoader,
        EnvironmentDetector $environmentDetector,
        Server $server,
        Filesystem $filesystem
    ) {
        $dotEnvConfigProcessor->beADoubleOf('Envariable\DotEnvConfigProcessor');
        $envariableConfigLoader->beADoubleOf('Envariable\EnvariableConfigLoader');
        $environmentDetector->beADoubleOf('Envariable\EnvironmentDetector');
        $server->beADoubleOf('Envariable\Util\Server');
        $filesystem->beADoubleOf('Envariable\Util\Filesystem');

        $this->envariableConfigLoader = $envariableConfigLoader;

        $this->beConstructedWith(
            $dotEnvConfigProcessor,
            $this->envariableConfigLoader,
            $environmentDetector,
            $server,
            $filesystem
        );
        $this->shouldHaveType('Envariable\Envariable');
    }

    /**
     * Test that Envariable bootstraps all of its components
     *
     * @param \Envariable\EnvironmentDetector
     */
    function it_executes_the_bootstrapping_of_all_of_the_envariable_components(EnvironmentDetector $environmentDetector)
    {
        $this
            ->envariableConfigLoader
            ->loadConfigFile()
            ->willReturn(array(
                'production' => array(
                    'hostname' => 'some-hostname',
                ),
            ));

        $this->execute();
        $this->getEnvironmentDetector()->shouldReturn($environmentDetector);
    }
}

<?php

namespace spec\Envariable;

use Envariable\DotEnvConfigProcessor;
use Envariable\ConfigLoader;
use Envariable\EnvironmentDetector;
use Envariable\Util\Server;
use Envariable\Util\Filesystem;
use PhpSpec\ObjectBehavior;

/**
 * Envariable Test.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class EnvariableSpec extends ObjectBehavior
{
    /**
     * @var \Envariable\ConfigLoader
     */
    private $configLoader;

    /**
     * Pre-test setup.
     *
     * @param \Envariable\DotEnvConfigProcessor $dotEnvConfigProcessor
     * @param \Envariable\ConfigLoader          $configLoader
     * @param \Envariable\EnvironmentDetector   $environmentDetector
     * @param \Envariable\Util\Server           $server
     * @param \Envariable\Util\Filesystem       $filesystem
     */
    function let(
        DotEnvConfigProcessor $dotEnvConfigProcessor,
        ConfigLoader $configLoader,
        EnvironmentDetector $environmentDetector,
        Server $server,
        Filesystem $filesystem
    ) {
        $dotEnvConfigProcessor->beADoubleOf('Envariable\DotEnvConfigProcessor');
        $configLoader->beADoubleOf('Envariable\ConfigLoader');
        $environmentDetector->beADoubleOf('Envariable\EnvironmentDetector');
        $server->beADoubleOf('Envariable\Util\Server');
        $filesystem->beADoubleOf('Envariable\Util\Filesystem');

        $this->configLoader = $configLoader;

        $this->beConstructedWith(
            $dotEnvConfigProcessor,
            $this->configLoader,
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
            ->configLoader
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

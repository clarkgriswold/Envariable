<?php

namespace spec\Envariable;

use Envariable\CustomConfigProcessor;
use Envariable\EnvariableConfigLoader;
use Envariable\Environment;
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
     * @param \Envariable\CustomConfigProcessor  $customConfigProcessor
     * @param \Envariable\EnvariableConfigLoader $envariableConfigLoader
     * @param \Envariable\Environment            $environment
     * @param \Envariable\Util\Server            $server
     * @param \Envariable\Util\Filesystem        $filesystem
     */
    function let(
        CustomConfigProcessor $customConfigProcessor,
        EnvariableConfigLoader $envariableConfigLoader,
        Environment $environment,
        Server $server,
        Filesystem $filesystem
    ) {
        $customConfigProcessor->beADoubleOf('Envariable\CustomConfigProcessor');
        $envariableConfigLoader->beADoubleOf('Envariable\EnvariableConfigLoader');
        $environment->beADoubleOf('Envariable\Environment');
        $server->beADoubleOf('Envariable\Util\Server');
        $filesystem->beADoubleOf('Envariable\Util\Filesystem');

        $this->envariableConfigLoader = $envariableConfigLoader;

        $this->beConstructedWith(
            $customConfigProcessor,
            $this->envariableConfigLoader,
            $environment,
            $server,
            $filesystem
        );
        $this->shouldHaveType('Envariable\Envariable');
    }

    /**
     * Test that Envariable bootstraps all of its components
     *
     * @param \Envariable\Environment
     */
    function it_executes_the_bootstrapping_of_all_of_the_envariable_components(Environment $environment)
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
        $this->getEnvironment()->shouldReturn($environment);
    }
}

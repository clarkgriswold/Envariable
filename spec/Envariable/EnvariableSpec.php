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
     * @var \Envaraible\Util\Filesystem
     */
    private $filesystem;

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

        $this->filesystem = $filesystem;

        $this->beConstructedWith(
            $customConfigProcessor,
            $envariableConfigLoader,
            $environment,
            $server,
            $this->filesystem
        );
        $this->shouldHaveType('Envariable\Envariable');
    }

    function it_does_stuff(Environment $environment)
    {
        //$this->filesystem->getApplicationRootPath()->willReturn('ding');
        $this->getEnvironment()->shouldReturn($environment);
    }

    /*function it_does_other_stuff()
    {
        $this->filesystem->getApplicationRootPath()->willReturn('dong');
    }*/
}

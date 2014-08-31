<?php
/**
 * @copyright 2014
 */

namespace spec\Envariable;

use Envariable\Helpers\EnvironmentHelper;
use Envariable\Helpers\ServerInterfaceHelper;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Environment Test.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class EnvironmentSpec extends ObjectBehavior
{
    /**
     * @var array
     */
    private static $configMap = array(
        'environmentToHostnameMap' => array(
            'production-without-subdomain' => 'production.machine-name.without-subdomain-matching',
            'production-with-subdomain'    => array(
                'hostname'  => 'production.machine-name.with-subdomain-matching',
                'subdomain' => 'www',
            ),
            'testing-with-subdomain' => array(
                'hostname'  => 'testing.machine-name.with-subdomain-matching',
                'subdomain' => 'testing',
            ),
        ),
        'cliDefaultEnvironment' => 'production',
    );

    /**
     * Pre-test setup
     */
    function let()
    {
        $this->beConstructedWith(self::$configMap);
        $this->shouldHaveType('Envariable\Environment');
    }

    /**
     * Test that defineEnvironment is called with 'production-without-subdomain'.
     *
     * @param \Envariable\Helpers\ServerInterfaceHelper $serverInterfaceHelper
     * @param \Envariable\Helpers\EnvironmentHelper     $environmentHelper
     */
    function it_calls_define_environment_with_argument_production_without_subdomain(
        ServerInterfaceHelper $serverInterfaceHelper,
        EnvironmentHelper $environmentHelper
    ) {
        $_SERVER['SERVER_NAME'] = 'www.example.com';

        $serverInterfaceHelper
            ->getType()
            ->willReturn('apache2handler');

        $environmentHelper
            ->getHostname()
            ->willReturn(self::$configMap['environmentToHostnameMap']['production-without-subdomain']);

        $environmentHelper
            ->defineEnvironment(Argument::exact('production-without-subdomain'))
            ->shouldBeCalled();

        $environmentHelper
            ->verifyEnvironment()
            ->shouldBeCalled();

        $this->setServerInterfaceHelper($serverInterfaceHelper);
        $this->setEnvironmentHelper($environmentHelper);

        $this->shouldNotThrow('\Exception')->duringDetect();
    }

    /**
     * Test that defineEnvironment is called with 'production-with-subdomain'.
     *
     * @param \Envariable\Helpers\ServerInterfaceHelper $serverInterfaceHelper
     * @param \Envariable\Helpers\EnvironmentHelper     $environmentHelper
     */
    function it_calls_define_environment_with_argument_production_with_subdomain(
        ServerInterfaceHelper $serverInterfaceHelper,
        EnvironmentHelper $environmentHelper
    ) {
        $_SERVER['SERVER_NAME'] = 'www.example.com';

        $serverInterfaceHelper
            ->getType()
            ->willReturn('apache2handler');

        $environmentHelper
            ->getHostname()
            ->willReturn(self::$configMap['environmentToHostnameMap']['production-with-subdomain']['hostname']);

        $environmentHelper
            ->defineEnvironment(Argument::exact('production-with-subdomain'))
            ->shouldBeCalled();

        $environmentHelper
            ->verifyEnvironment()
            ->shouldBeCalled();

        $this->setServerInterfaceHelper($serverInterfaceHelper);
        $this->setEnvironmentHelper($environmentHelper);

        $this->shouldNotThrow('\Exception')->duringDetect();
    }

    /**
     * Test that defineEnvironment is called with 'testing-with-subdomain'.
     *
     * @param \Envariable\Helpers\ServerInterfaceHelper $serverInterfaceHelper
     * @param \Envariable\Helpers\EnvironmentHelper     $environmentHelper
     */
    function it_calls_define_environment_with_argument_testing_with_subdomain(
        ServerInterfaceHelper $serverInterfaceHelper,
        EnvironmentHelper $environmentHelper
    ) {
        $_SERVER['SERVER_NAME'] = 'testing.example.com';

        $serverInterfaceHelper
            ->getType()
            ->willReturn('apache2handler');

        $environmentHelper
            ->getHostname()
            ->willReturn(self::$configMap['environmentToHostnameMap']['testing-with-subdomain']['hostname']);

        $environmentHelper
            ->defineEnvironment(Argument::exact('testing-with-subdomain'))
            ->shouldBeCalled();

        $environmentHelper
            ->verifyEnvironment()
            ->shouldBeCalled();

        $this->setServerInterfaceHelper($serverInterfaceHelper);
        $this->setEnvironmentHelper($environmentHelper);

        $this->shouldNotThrow('\Exception')->duringDetect();
    }

    /**
     * Test that defineEnvironment is called with 'production' CLI mode.
     *
     * @param \Envariable\Helpers\ServerInterfaceHelper $serverInterfaceHelper
     * @param \Envariable\Helpers\EnvironmentHelper     $environmentHelper
     */
    function it_calls_define_environment_with_argument_production_cli_mode(
        ServerInterfaceHelper $serverInterfaceHelper,
        EnvironmentHelper $environmentHelper
    ) {
        $_SERVER['SERVER_NAME'] = 'testing.example.com';

        $serverInterfaceHelper
            ->getType()
            ->willReturn('cli');

        $environmentHelper
            ->getHostname()
            ->willReturn(self::$configMap['cliDefaultEnvironment']);

        $environmentHelper
            ->defineEnvironment(Argument::exact('production'))
            ->shouldBeCalled();

        $environmentHelper
            ->verifyEnvironment()
            ->shouldBeCalled();

        $this->setServerInterfaceHelper($serverInterfaceHelper);
        $this->setEnvironmentHelper($environmentHelper);

        $this->shouldNotThrow('\Exception')->duringDetect();
    }
}

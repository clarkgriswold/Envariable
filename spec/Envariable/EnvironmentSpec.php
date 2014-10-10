<?php

namespace spec\Envariable;

use Envariable\HostnameStrategy;
use Envariable\HostnameServernameStrategy;
use Envariable\ServernameStrategy;
use Envariable\Util\Server;
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
        'environmentToDetectionMethodMap' => array(
            'production-without-servername-matching' => array(
                'hostname'   => 'production.machine-name.without-servername-matching',
            ),
            'production-with-servername-matching'    => array(
                'hostname'   => 'production.machine-name.with-servername-matching',
                'servername' => 'www',
            ),
            'testing-with-servername-matching'       => array(
                'hostname'   => 'testing.machine-name.with-servername-matching',
                'servername' => 'testing',
            ),
            'staging-without-hostname-matching'      => array(
                'servername' => 'staging',
            ),
        ),
        'cliDefaultEnvironment' => 'production',
    );

    /**
     * @var array
     */
    private static $environmentValidationStrategyMap = array();

    /**
     * Set-up
     */
    function let()
    {
        self::$environmentValidationStrategyMap['HostnameStrategy']           = new HostnameStrategy();
        self::$environmentValidationStrategyMap['HostnameServernameStrategy'] = new HostnameServernameStrategy();
        self::$environmentValidationStrategyMap['ServernameStrategy']         = new ServernameStrategy();
    }

    /**
     * Test that the SUT is initializable.
     */
    function it_is_initializable()
    {
        $this->shouldHaveType('Envariable\Environment');
    }

    /**
     * Test that exception not thrown and that getDetectedEnvironment returns 'production-without-servername-matching'.
     *
     * @param \Envariable\Util\Server $server
     */
    function it_will_return_production_without_servername_matching(Server $server)
    {
        $_SERVER['SERVER_NAME'] = 'www.example.com';

        $server
            ->getInterfaceType()
            ->willReturn('apache2handler');

        $server
            ->getHostname()
            ->willReturn(self::$configMap['environmentToDetectionMethodMap']['production-without-servername-matching']['hostname']);

        $this->setConfiguration(self::$configMap);
        $this->setServer($server);
        $this->setEnvironmentValidationStrategyMap(self::$environmentValidationStrategyMap);

        $this->shouldNotThrow('\Exception')->duringDetect();
        $this->getDetectedEnvironment()->shouldReturn('production-without-servername-matching');
    }

    /**
     * Test that exception not thrown and that getDetectedEnvironment returns 'production-with-servername-matching'.
     *
     * @param \Envariable\Util\Server $server
     */
    function it_will_return_production_with_servername_matching(Server $server)
    {
        $_SERVER['SERVER_NAME'] = 'www.example.com';

        $server
            ->getInterfaceType()
            ->willReturn('apache2handler');

        $server
            ->getHostname()
            ->willReturn(self::$configMap['environmentToDetectionMethodMap']['production-with-servername-matching']['hostname']);

        $this->setConfiguration(self::$configMap);
        $this->setServer($server);
        $this->setEnvironmentValidationStrategyMap(self::$environmentValidationStrategyMap);

        $this->shouldNotThrow('\Exception')->duringDetect();
        $this->getDetectedEnvironment()->shouldReturn('production-with-servername-matching');
    }

    /**
     * Test that exception not thrown and that getDetectedEnvironment returns 'testing-with-servername-matching'.
     *
     * @param \Envariable\Util\Server $server
     */
    function it_will_return_testing_with_servername_matching(Server $server)
    {
        $_SERVER['SERVER_NAME'] = 'testing.example.com';

        $server
            ->getInterfaceType()
            ->willReturn('apache2handler');

        $server
            ->getHostname()
            ->willReturn(self::$configMap['environmentToDetectionMethodMap']['testing-with-servername-matching']['hostname']);

        $this->setConfiguration(self::$configMap);
        $this->setServer($server);
        $this->setEnvironmentValidationStrategyMap(self::$environmentValidationStrategyMap);

        $this->shouldNotThrow('\Exception')->duringDetect();
        $this->getDetectedEnvironment()->shouldReturn('testing-with-servername-matching');
    }

    /**
     * Test that exception not thrown and that getDetectedEnvironment returns 'staging-without-hostname-matching'.
     *
     * @param \Envariable\Util\Server $server
     */
    function it_will_return_staging_without_hostname_matching(Server $server)
    {
        $_SERVER['SERVER_NAME'] = 'staging.example.com';

        $server
            ->getInterfaceType()
            ->willReturn('apache2handler');

        $server
            ->getHostname()
            ->willReturn('staging.machine-name.without-hostname-matching');

        $this->setConfiguration(self::$configMap);
        $this->setServer($server);
        $this->setEnvironmentValidationStrategyMap(self::$environmentValidationStrategyMap);

        $this->shouldNotThrow('\Exception')->duringDetect();
        $this->getDetectedEnvironment()->shouldReturn('staging-without-hostname-matching');
    }

    /**
     * Test that exception not thrown and that getDetectedEnvironment returns 'production' in CLI mode.
     *
     * @param \Envariable\Util\Server $server
     */
    function it_will_return_production_in_cli_mode(Server $server)
    {
        $_SERVER['SERVER_NAME'] = 'testing.example.com';

        $server
            ->getInterfaceType()
            ->willReturn('cli');

        $server
            ->getHostname()
            ->willReturn(self::$configMap['cliDefaultEnvironment']);

        $this->setConfiguration(self::$configMap);
        $this->setServer($server);
        $this->setEnvironmentValidationStrategyMap(self::$environmentValidationStrategyMap);

        $this->shouldNotThrow('\Exception')->duringDetect();
        $this->getDetectedEnvironment()->shouldReturn('production');
    }

    /**
     * Test that exception is thrown as the cliDefaultEnvironment config option is empty.
     *
     * @param \Envariable\Util\Server $server
     */
    function it_throws_exception_as_cli_default_environment_is_empty(Server $server)
    {
        $server
            ->getInterfaceType()
            ->willReturn('apache2handler');

        $this->setConfiguration(array(
            'cliDefaultEnvironment' => '',
        ));
        $this->setServer($server);

        $this->shouldThrow(new \RuntimeException('cliDefaultEnvironment must contain a value within Envariable config.'))->duringDetect();
        $this->getDetectedEnvironment()->shouldReturn(null);
    }

    /**
     * Test that exception is thrown as the environmentToDetectionMethodMap is empty.
     *
     * @param \Envariable\Util\Server $server
     */
    function it_throws_exception_as_environment_to_hostname_map_is_empty(Server $server)
    {
        $server
            ->getInterfaceType()
            ->willReturn('apache2handler');

        $this->setConfiguration(array(
            'environmentToDetectionMethodMap' => array(),
            'cliDefaultEnvironment'           => 'production',
        ));
        $this->setServer($server);

        $this->shouldThrow(new \RuntimeException('You have not defined any hostnames within the "environmentToDetectionMethodMap" array within Envariable config.'))->duringDetect();
        $this->getDetectedEnvironment()->shouldReturn(null);
    }

    /**
     * Test that exception is thrown as there is no hostname match and the environment is not defined.
     *
     * @param \Envariable\Util\Server $server
     */
    function it_throws_exception_as_no_hostname_match_and_environment_not_defined(Server $server)
    {
        $server
            ->getInterfaceType()
            ->willReturn('apache2handler');

        $server
            ->getHostname()
            ->willReturn(self::$configMap['environmentToDetectionMethodMap']['production-without-servername-matching']['hostname']);

        $this->setConfiguration(array(
            'environmentToDetectionMethodMap' => array(
                'production-without-servername-matching' => array(
                    'hostname' => 'this-will-not-match',
                ),
            ),
            'cliDefaultEnvironment' => 'production',
        ));
        $this->setServer($server);
        $this->setEnvironmentValidationStrategyMap(self::$environmentValidationStrategyMap);

        $this->shouldThrow(new \RuntimeException('Could not detect the environment.'))->duringDetect();
        $this->getDetectedEnvironment()->shouldReturn(null);
    }

    /**
     * Test that exception is thrown as there are too many matches.
     *
     * @param \Envariable\Util\Server $server
     */
    function it_throws_exception_as_there_are_one_too_many_matches(Server $server)
    {
        $_SERVER['SERVER_NAME'] = 'www.example.com';

        $server
            ->getInterfaceType()
            ->willReturn('apache2handler');

        $server
            ->getHostname()
            ->willReturn(self::$configMap['environmentToDetectionMethodMap']['production-without-servername-matching']['hostname']);

        $this->setConfiguration(array(
            'environmentToDetectionMethodMap' => array(
                'production' => array(
                    'hostname' => 'production.machine-name.without-servername-matching',
                ),
                'production-redundant-entry' => array(
                    'hostname' => 'production.machine-name.without-servername-matching',
                ),
            ),
            'cliDefaultEnvironment' => 'production',
        ));
        $this->setServer($server);
        $this->setEnvironmentValidationStrategyMap(self::$environmentValidationStrategyMap);

        $this->shouldThrow(new \RuntimeException('Could not detect the environment.'))->duringDetect();
        $this->getDetectedEnvironment()->shouldReturn(null);
    }

    /**
     * Test that exception is thrown as hostname and servername configuration are invalid.
     *
     * @param \Envariable\Util\Server $server
     */
    function it_throws_exception_as_hostname_is_invalid(Server $server)
    {
        $server
            ->getInterfaceType()
            ->willReturn('apache2handler');

        $this->setConfiguration(array(
            'environmentToDetectionMethodMap' => array(
                'production' => array(
                    'misspelled-hostname-servername-key' => 'production.machine-name.without-servername-matching',
                ),
            ),
            'cliDefaultEnvironment' => 'production',
        ));
        $this->setServer($server);
        $this->setEnvironmentValidationStrategyMap(self::$environmentValidationStrategyMap);

        $this->shouldThrow(new \RuntimeException('Invalid hostname or servername configuration.'))->duringDetect();
        $this->getDetectedEnvironment()->shouldReturn(null);
    }
}

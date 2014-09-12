<?php

namespace spec\Envariable;

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
        'environmentToHostnameMap' => array(
            'production-without-subdomain-matching' => 'production.machine-name.without-subdomain-matching',
            'production-with-subdomain-matching'    => array(
                'hostname'  => 'production.machine-name.with-subdomain-matching',
                'subdomain' => 'www',
            ),
            'testing-with-subdomain-matching' => array(
                'hostname'  => 'testing.machine-name.with-subdomain-matching',
                'subdomain' => 'testing',
            ),
        ),
        'cliDefaultEnvironment' => 'production',
    );

    /**
     * Test that the SUT is initializable.
     */
    function it_is_initializable()
    {
        $this->shouldHaveType('Envariable\Environment');
    }

    /**
     * Test that exception not thrown and that getDetectedEnvironment returns 'production-without-subdomain-matching'.
     *
     * @param \Envariable\Util\Server $server
     */
    function it_will_return_production_without_subdomain_matching(Server $server)
    {
        $_SERVER['SERVER_NAME'] = 'www.example.com';

        $server
            ->getInterfaceType()
            ->willReturn('apache2handler');

        $server
            ->getHostname()
            ->willReturn(self::$configMap['environmentToHostnameMap']['production-without-subdomain-matching']);

        $this->setConfiguration(self::$configMap);
        $this->setServer($server);

        $this->shouldNotThrow('\Exception')->duringDetect();
        $this->getDetectedEnvironment()->shouldReturn('production-without-subdomain-matching');
    }

    /**
     * Test that exception not thrown and that getDetectedEnvironment returns 'production-with-subdomain-matching'.
     *
     * @param \Envariable\Util\Server $server
     */
    function it_will_return_production_with_subdomain_matching(Server $server)
    {
        $_SERVER['SERVER_NAME'] = 'www.example.com';

        $server
            ->getInterfaceType()
            ->willReturn('apache2handler');

        $server
            ->getHostname()
            ->willReturn(self::$configMap['environmentToHostnameMap']['production-with-subdomain-matching']['hostname']);

        $this->setConfiguration(self::$configMap);
        $this->setServer($server);

        $this->shouldNotThrow('\Exception')->duringDetect();
        $this->getDetectedEnvironment()->shouldReturn('production-with-subdomain-matching');
    }

    /**
     * Test that exception not thrown and that getDetectedEnvironment returns 'testing-with-subdomain-matching'.
     *
     * @param \Envariable\Util\Server $server
     */
    function it_will_return_testing_with_subdomain_matching(Server $server)
    {
        $_SERVER['SERVER_NAME'] = 'testing.example.com';

        $server
            ->getInterfaceType()
            ->willReturn('apache2handler');

        $server
            ->getHostname()
            ->willReturn(self::$configMap['environmentToHostnameMap']['testing-with-subdomain-matching']['hostname']);

        $this->setConfiguration(self::$configMap);
        $this->setServer($server);

        $this->shouldNotThrow('\Exception')->duringDetect();
        $this->getDetectedEnvironment()->shouldReturn('testing-with-subdomain-matching');
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

        $this->shouldThrow(new \Exception('cliDefaultEnvironment must contain a value within Envariable config.'))->duringDetect();
    }

    /**
     * Test that exception is thrown as the environmentToHostnameMap is empty.
     *
     * @param \Envariable\Util\Server $server
     */
    function it_throws_exception_as_environment_to_hostname_map_is_empty(Server $server)
    {
        $server
            ->getInterfaceType()
            ->willReturn('apache2handler');

        $this->setConfiguration(array(
            'environmentToHostnameMap' => array(),
            'cliDefaultEnvironment'    => 'production',
        ));
        $this->setServer($server);

        $this->shouldThrow(new \Exception('You have not defined any hostnames within the "environmentToHostnameMap" array within Envariable config.'))->duringDetect();
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
            ->willReturn(self::$configMap['environmentToHostnameMap']['production-without-subdomain-matching']);

        $this->setConfiguration(array(
            'environmentToHostnameMap' => array(
                'production-without-subdomain' => 'this-will-not-match',
            ),
            'cliDefaultEnvironment'    => 'production',
        ));
        $this->setServer($server);

        $this->shouldThrow(new \Exception('Could not detect the environment.'))->duringDetect();
    }
}

<?php
/**
 * @copyright 2014
 */

namespace spec\Envariable;

use Envariable\Helpers\PathHelper;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Envariable Test.
 *
 * @author  Mark kasaboski <mark.kasaboski@gmail.com>
 */
class EnvariableSpec extends ObjectBehavior
{
    /**
     * {@inheritdoc}
     */
    public function getMatchers()
    {
        return array(
            'haveEnvironmentVariable' => function ($subject, array $data) {
                $key = key($data);

                return getenv($key) === $data[$key] && $_ENV[$key] === $data[$key];
            }
        );
    }

    /**
     * Test that Envariable is initializable
     */
    function it_is_initializable()
    {
        $this->shouldHaveType('Envariable\Envariable');
    }

    /**
     * Test that custom config data is defined as environment variables.
     *
     * @param \Envariable\Helpers\PathHelper $pathHelper
     */
    function it_will_define_custom_config_as_environment_variables(PathHelper $pathHelper)
    {
        $environment = 'production';

        $pathHelper
            ->getApplicationRootPath()
            ->willReturn(__DIR__ . '/Config');

        $this->setConfiguration(array(
            'customEnvironmentConfigPath' => null,
        ));
        $this->setEnvironment($environment);
        $this->setPathHelper($pathHelper);

        $this->shouldNotThrow('\Exception')->duringPutEnv();

        $this->shouldHaveEnvironmentVariable(array('SOMEPRODUCTIONDB_HOST' => 'some-host'));
        $this->shouldHaveEnvironmentVariable(array('SOMEPRODUCTIONSERVICE_EMAIL' => 'some-email-address@example.com'));
        $this->shouldHaveEnvironmentVariable(array('SOMEPRODUCTIONOTHER_NESTEDCONFIG_SHOULDYOUNEEDTHIS' => 'it-is-handled'));
    }

    /**
     * Test that custom config data, located in a custom path, is defined as environment variables.
     *
     * @param \Envariable\Helpers\PathHelper $pathHelper
     */
    function it_will_define_custom_config_as_environment_variables_from_a_custom_config_path(PathHelper $pathHelper)
    {
        $environment = 'production';

        $pathHelper
            ->getApplicationRootPath()
            ->willReturn(__DIR__ . '/Config');

        $this->setConfiguration(array(
            'customEnvironmentConfigPath' => '../../PretendOutsideRootConfig/',
        ));
        $this->setEnvironment($environment);
        $this->setPathHelper($pathHelper);

        $this->shouldNotThrow('\Exception')->duringPutEnv();

        $this->shouldHaveEnvironmentVariable(array('SOMEPRODUCTIONDBOUTSIDEROOT_HOST' => 'some-host'));
        $this->shouldHaveEnvironmentVariable(array('SOMEPRODUCTIONSERVICEOUTSIDEROOT_EMAIL' => 'some-email-address@example.com'));
    }

    /**
     * Test that an exception is thrown when the custom config file cannot be found.
     *
     * @param \Envariable\Helpers\PathHelper $pathHelper
     */
    function it_will_throw_exception_when_custom_configuration_file_not_found(PathHelper $pathHelper)
    {
        $environment = 'production';

        $pathHelper
            ->getApplicationRootPath()
            ->willReturn('invalid_path');

        $this->setConfiguration(array(
            'customEnvironmentConfigPath' => null,
        ));
        $this->setEnvironment($environment);
        $this->setPathHelper($pathHelper);

        $this->shouldThrow(new \Exception('Could not find configuration file: [invalid_path/.env.production.php]'))->duringPutEnv();
    }

    /**
     * Test that an exception is thrown when the custom config file does not contain any configuration data.
     *
     * @param \Envariable\Helpers\PathHelper $pathHelper
     */
    function it_will_throw_exception_when_custom_configuration_is_empty(PathHelper $pathHelper)
    {
        $environment = 'empty';

        $pathHelper
            ->getApplicationRootPath()
            ->willReturn(__DIR__ . '/Config');

        $this->setConfiguration(array(
            'customEnvironmentConfigPath' => null,
        ));
        $this->setEnvironment($environment);
        $this->setPathHelper($pathHelper);

        $this->shouldThrow(new \Exception('Your custom environment config is empty.'))->duringPutEnv();
    }

    /**
     * Test that an exception is throw when key already exists within the $_ENV superglobal.
     *
     * @param \Envariable\Helpers\PathHelper $pathHelper
     */
    function it_will_throw_exception_when_key_already_exists_within_env_superglobal(PathHelper $pathHelper)
    {
        $_ENV['SOMEENVDUPEDB_HOST'] = 'Too late! I got here first.';
        $environment                = 'envdupe';

        $pathHelper
            ->getApplicationRootPath()
            ->willReturn(__DIR__ . '/Config');

        $this->setConfiguration(array(
            'customEnvironmentConfigPath' => null,
        ));
        $this->setEnvironment($environment);
        $this->setPathHelper($pathHelper);

        $this->shouldThrow(new \Exception('An environment variable with the key "SOMEENVDUPEDB_HOST" already exists. Aborting.'))->duringPutEnv();
    }

    /**
     * Test that an exception is throw when name already exists within the environment store.
     *
     * @param \Envariable\Helpers\PathHelper $pathHelper
     */
    function it_will_throw_exception_when_name_already_exists_within_getenv_environment_store(PathHelper $pathHelper)
    {
        putenv('SOMEGETENVDUPEDB_HOST=Too late! I got here first.');

        $environment = 'getenvdupe';

        $pathHelper
            ->getApplicationRootPath()
            ->willReturn(__DIR__ . '/Config');

        $this->setConfiguration(array(
            'customEnvironmentConfigPath' => null,
        ));
        $this->setEnvironment($environment);
        $this->setPathHelper($pathHelper);

        $this->shouldThrow(new \Exception('An environment variable with the name "SOMEGETENVDUPEDB_HOST" already exists. Aborting.'))->duringPutEnv();
    }
}

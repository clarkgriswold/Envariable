<?php

namespace spec\Envariable;

use Envariable\Util\Filesystem;
use PhpSpec\ObjectBehavior;

/**
 * .env Config Processor Test.
 *
 * @author Mark kasaboski <mark.kasaboski@gmail.com>
 */
class DotEnvConfigProcessorSpec extends ObjectBehavior
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
     * Test that the SUT is initializable.
     */
    function it_is_initializable()
    {
        $this->shouldHaveType('Envariable\DotEnvConfigProcessor');
    }

    /**
     * Test that custom config data is defined as environment variables.
     *
     * @param \Envariable\Util\Filesystem $filesystem
     */
    function it_will_define_custom_config_as_environment_variables(Filesystem $filesystem)
    {
        $environment = 'production';

        $filesystem
            ->getApplicationRootPath()
            ->willReturn(__DIR__ . '/Config');

        $this->setConfiguration(array(
            'customEnvironmentConfigPath' => null,
        ));
        $this->setEnvironment($environment);
        $this->setFilesystem($filesystem);

        $this->shouldNotThrow('\RuntimeException')->duringExecute();

        $this->shouldHaveEnvironmentVariable(array('SOMEPRODUCTIONDB_HOST' => 'some-host'));
        $this->shouldHaveEnvironmentVariable(array('SOMEPRODUCTIONSERVICE_EMAIL' => 'some-email-address@example.com'));
        $this->shouldHaveEnvironmentVariable(array('SOMEPRODUCTIONOTHER_NESTEDCONFIG_SHOULDYOUNEEDTHIS' => 'it-is-handled'));
    }

    /**
     * Test that custom config data, located in a custom path, is defined as environment variables.
     *
     * @param \Envariable\Util\Filesystem $filesystem
     */
    function it_will_define_custom_config_as_environment_variables_from_a_custom_config_path(Filesystem $filesystem)
    {
        $environment = 'production';

        $filesystem
            ->getApplicationRootPath()
            ->willReturn(__DIR__ . '/Config');

        $this->setConfiguration(array(
            'customEnvironmentConfigPath' => '../../PretendOutsideRootConfig/',
        ));
        $this->setEnvironment($environment);
        $this->setFilesystem($filesystem);

        $this->shouldNotThrow('\RuntimeException')->duringExecute();

        $this->shouldHaveEnvironmentVariable(array('SOMEPRODUCTIONDBOUTSIDEROOT_HOST' => 'some-host'));
        $this->shouldHaveEnvironmentVariable(array('SOMEPRODUCTIONSERVICEOUTSIDEROOT_EMAIL' => 'some-email-address@example.com'));
    }

    /**
     * Test that an exception is thrown when the custom config file cannot be found.
     *
     * @param \Envariable\Util\Filesystem $filesystem
     */
    function it_will_throw_exception_when_custom_configuration_file_not_found(Filesystem $filesystem)
    {
        $environment = 'production';

        $filesystem
            ->getApplicationRootPath()
            ->willReturn('invalid_path');

        $this->setConfiguration(array(
            'customEnvironmentConfigPath' => null,
        ));
        $this->setEnvironment($environment);
        $this->setFilesystem($filesystem);

        $this->shouldThrow(new \RuntimeException('Could not find configuration file: [invalid_path/.env.production.php]'))->duringExecute();
    }

    /**
     * Test that an exception is thrown when the custom config file does not contain any configuration data.
     *
     * @param \Envariable\Util\Filesystem $filesystem
     */
    function it_will_throw_exception_when_custom_configuration_is_empty(Filesystem $filesystem)
    {
        $environment = 'empty';

        $filesystem
            ->getApplicationRootPath()
            ->willReturn(__DIR__ . '/Config');

        $this->setConfiguration(array(
            'customEnvironmentConfigPath' => null,
        ));
        $this->setEnvironment($environment);
        $this->setFilesystem($filesystem);

        $this->shouldThrow(new \RuntimeException('Your custom environment config is empty.'))->duringExecute();
    }

    /**
     * Test that an exception is throw when key already exists within the $_ENV superglobal.
     *
     * @param \Envariable\Util\Filesystem $filesystem
     */
    function it_will_throw_exception_when_key_already_exists_within_env_superglobal(Filesystem $filesystem)
    {
        $_ENV['SOMEENVDUPEDB_HOST'] = 'Too late! I got here first.';
        $environment                = 'envdupe';

        $filesystem
            ->getApplicationRootPath()
            ->willReturn(__DIR__ . '/Config');

        $this->setConfiguration(array(
            'customEnvironmentConfigPath' => null,
        ));
        $this->setEnvironment($environment);
        $this->setFilesystem($filesystem);

        $this->shouldThrow(new \RuntimeException('An environment variable with the key "SOMEENVDUPEDB_HOST" already exists. Aborting.'))->duringExecute();
    }

    /**
     * Test that an exception is throw when name already exists within the environment store.
     *
     * @param \Envariable\Util\Filesystem $filesystem
     */
    function it_will_throw_exception_when_name_already_exists_within_getenv_environment_store(Filesystem $filesystem)
    {
        putenv('SOMEGETENVDUPEDB_HOST=Too late! I got here first.');

        $environment = 'getenvdupe';

        $filesystem
            ->getApplicationRootPath()
            ->willReturn(__DIR__ . '/Config');

        $this->setConfiguration(array(
            'customEnvironmentConfigPath' => null,
        ));
        $this->setEnvironment($environment);
        $this->setFilesystem($filesystem);

        $this->shouldThrow(new \RuntimeException('An environment variable with the name "SOMEGETENVDUPEDB_HOST" already exists. Aborting.'))->duringExecute();
    }
}

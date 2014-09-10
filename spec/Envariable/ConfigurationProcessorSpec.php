<?php
/**
 * @copyright 2014
 */

namespace spec\Envariable;

use Envariable\Util\FileSystemUtil;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Configuration Processor Test.
 *
 * @author Mark kasaboski <mark.kasaboski@gmail.com>
 */
class ConfigurationProcessorSpec extends ObjectBehavior
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
        $this->shouldHaveType('Envariable\ConfigurationProcessor');
    }

    /**
     * Test that custom config data is defined as environment variables.
     *
     * @param \Envariable\Util\FileSystemUtil $fileSystemUtil
     */
    function it_will_define_custom_config_as_environment_variables(FileSystemUtil $fileSystemUtil)
    {
        $environment = 'production';

        $fileSystemUtil
            ->getApplicationRootPath()
            ->willReturn(__DIR__ . '/Config');

        $this->setConfiguration(array(
            'customEnvironmentConfigPath' => null,
        ));
        $this->setEnvironment($environment);
        $this->setFileSystemUtil($fileSystemUtil);

        $this->shouldNotThrow('\Exception')->duringExecute();

        $this->shouldHaveEnvironmentVariable(array('SOMEPRODUCTIONDB_HOST' => 'some-host'));
        $this->shouldHaveEnvironmentVariable(array('SOMEPRODUCTIONSERVICE_EMAIL' => 'some-email-address@example.com'));
        $this->shouldHaveEnvironmentVariable(array('SOMEPRODUCTIONOTHER_NESTEDCONFIG_SHOULDYOUNEEDTHIS' => 'it-is-handled'));
    }

    /**
     * Test that custom config data, located in a custom path, is defined as environment variables.
     *
     * @param \Envariable\Util\FileSystemUtil $fileSystemUtil
     */
    function it_will_define_custom_config_as_environment_variables_from_a_custom_config_path(FileSystemUtil $fileSystemUtil)
    {
        $environment = 'production';

        $fileSystemUtil
            ->getApplicationRootPath()
            ->willReturn(__DIR__ . '/Config');

        $this->setConfiguration(array(
            'customEnvironmentConfigPath' => '../../PretendOutsideRootConfig/',
        ));
        $this->setEnvironment($environment);
        $this->setFileSystemUtil($fileSystemUtil);

        $this->shouldNotThrow('\Exception')->duringExecute();

        $this->shouldHaveEnvironmentVariable(array('SOMEPRODUCTIONDBOUTSIDEROOT_HOST' => 'some-host'));
        $this->shouldHaveEnvironmentVariable(array('SOMEPRODUCTIONSERVICEOUTSIDEROOT_EMAIL' => 'some-email-address@example.com'));
    }

    /**
     * Test that an exception is thrown when the custom config file cannot be found.
     *
     * @param \Envariable\Util\FileSystemUtil $fileSystemUtil
     */
    function it_will_throw_exception_when_custom_configuration_file_not_found(FileSystemUtil $fileSystemUtil)
    {
        $environment = 'production';

        $fileSystemUtil
            ->getApplicationRootPath()
            ->willReturn('invalid_path');

        $this->setConfiguration(array(
            'customEnvironmentConfigPath' => null,
        ));
        $this->setEnvironment($environment);
        $this->setFileSystemUtil($fileSystemUtil);

        $this->shouldThrow(new \Exception('Could not find configuration file: [invalid_path/.env.production.php]'))->duringExecute();
    }

    /**
     * Test that an exception is thrown when the custom config file does not contain any configuration data.
     *
     * @param \Envariable\Util\FileSystemUtil $fileSystemUtil
     */
    function it_will_throw_exception_when_custom_configuration_is_empty(FileSystemUtil $fileSystemUtil)
    {
        $environment = 'empty';

        $fileSystemUtil
            ->getApplicationRootPath()
            ->willReturn(__DIR__ . '/Config');

        $this->setConfiguration(array(
            'customEnvironmentConfigPath' => null,
        ));
        $this->setEnvironment($environment);
        $this->setFileSystemUtil($fileSystemUtil);

        $this->shouldThrow(new \Exception('Your custom environment config is empty.'))->duringExecute();
    }

    /**
     * Test that an exception is throw when key already exists within the $_ENV superglobal.
     *
     * @param \Envariable\Util\FileSystemUtil $fileSystemUtil
     */
    function it_will_throw_exception_when_key_already_exists_within_env_superglobal(FileSystemUtil $fileSystemUtil)
    {
        $_ENV['SOMEENVDUPEDB_HOST'] = 'Too late! I got here first.';
        $environment                = 'envdupe';

        $fileSystemUtil
            ->getApplicationRootPath()
            ->willReturn(__DIR__ . '/Config');

        $this->setConfiguration(array(
            'customEnvironmentConfigPath' => null,
        ));
        $this->setEnvironment($environment);
        $this->setFileSystemUtil($fileSystemUtil);

        $this->shouldThrow(new \Exception('An environment variable with the key "SOMEENVDUPEDB_HOST" already exists. Aborting.'))->duringExecute();
    }

    /**
     * Test that an exception is throw when name already exists within the environment store.
     *
     * @param \Envariable\Util\FileSystemUtil $fileSystemUtil
     */
    function it_will_throw_exception_when_name_already_exists_within_getenv_environment_store(FileSystemUtil $fileSystemUtil)
    {
        putenv('SOMEGETENVDUPEDB_HOST=Too late! I got here first.');

        $environment = 'getenvdupe';

        $fileSystemUtil
            ->getApplicationRootPath()
            ->willReturn(__DIR__ . '/Config');

        $this->setConfiguration(array(
            'customEnvironmentConfigPath' => null,
        ));
        $this->setEnvironment($environment);
        $this->setFileSystemUtil($fileSystemUtil);

        $this->shouldThrow(new \Exception('An environment variable with the name "SOMEGETENVDUPEDB_HOST" already exists. Aborting.'))->duringExecute();
    }
}

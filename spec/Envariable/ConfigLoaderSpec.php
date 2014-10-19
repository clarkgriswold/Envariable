<?php

namespace spec\Envariable;

use Envariable\ConfigCreator;
use Envariable\FrameworkConfigPathLocatorCommands\BaseFrameworkConfigPathLocatorCommand;
use Envariable\FrameworkConfigPathLocatorCommands\FrameworkConfigPathLocatorCommandInterface;
use Envariable\Util\Filesystem;
use PhpSpec\ObjectBehavior;

/**
 * Envariable Config Loader Test.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class ConfigLoaderSpec extends ObjectBehavior
{
    /**
     * @var array
     */
    private static $commandList;

    /**
     * Set-up
     *
     * @param \Envariable\Util\Filesystem $filesystem
     */
    function let(Filesystem $filesystem)
    {
        self::$commandList = array(
            new TooHotCommand(),
            new TooColdCommand(),
            new JustRightCommand(),
        );

        foreach (self::$commandList as $command) {
            $command->setFilesystem($filesystem->getWrappedObject());
        }
    }

    /**
     * Test that the SUT is initializable.
     */
    function it_is_initializable()
    {
        $this->shouldHaveType('Envariable\ConfigLoader');
    }

    /**
     * Test that the config loader returns an array of config values.
     *
     * @param \Envariable\Util\Filesystem $filesystem
     */
    function it_will_return_an_array_of_config_values(Filesystem $filesystem)
    {
        for ($i = 0; $i < count(self::$commandList); $i++) {
            $this->addCommand(self::$commandList[$i]);
        }

        $configDirectoryPath      = 'path/to/config/directory';
        $envariableConfigFilePath = $configDirectoryPath . '/Envariable/config.php';
        $expectedResult           = array(
            'some-config-key' => 'some-config-value',
        );

        $filesystem
            ->fileExists($configDirectoryPath)
            ->willReturn(true);

        $filesystem
            ->fileExists($envariableConfigFilePath)
            ->willReturn(true);

        $filesystem
            ->loadConfigFile($envariableConfigFilePath)
            ->willReturn($expectedResult);

        $this->setFilesystem($filesystem);
        $this->loadConfigFile()->shouldReturn($expectedResult);
    }

    /**
     * Test that it will throw an exception with the message "Could not load Envariable config".
     *
     * @param \Envariable\Util\Filesystem $filesystem
     */
    function it_will_throw_a_could_not_load_envariable_config_exception(Filesystem $filesystem)
    {
        $this->addCommand(new TooHotCommand());
        $this->addCommand(new TooColdCommand());

        $filesystem
            ->fileExists(null)
            ->willReturn(false);

        $this->setFilesystem($filesystem);
        $this->shouldThrow(new \RuntimeException('Could not load Envariable config.'))->duringLoadConfigFile();
    }

    /**
     * Test that createConfigFile is called as envarible config file path does not exist.
     *
     * @param \Envariable\Util\Filesystem $filesystem
     * @param \Envariable\ConfigCreator   $configCreator
     */
    function it_will_call_createConfigFile_as_envariable_config_file_does_not_exist(Filesystem $filesystem, ConfigCreator $configCreator)
    {
        for ($i = 0; $i < count(self::$commandList); $i++) {
            $this->addCommand(self::$commandList[$i]);
        }

        $configDirectoryPath      = 'path/to/config/directory';
        $envariableConfigFilePath = $configDirectoryPath . '/Envariable/config.php';
        $expectedResult           = array(
            'some-config-key' => 'some-config-value',
        );

        $filesystem
            ->fileExists($configDirectoryPath)
            ->willReturn(true);

        $filesystem
            ->fileExists($envariableConfigFilePath)
            ->willReturn(false);

        $configCreator
            ->createConfigFile($envariableConfigFilePath)
            ->shouldBeCalled();

        $filesystem
            ->loadConfigFile($envariableConfigFilePath)
            ->willReturn($expectedResult);

        $this->setFilesystem($filesystem);
        $this->setConfigCreator($configCreator);
        $this->loadConfigFile()->shouldreturn($expectedResult);
    }
}

/**
 * TooHotCommand Stub.
 */
class TooHotCommand extends BaseFrameworkConfigPathLocatorCommand implements FrameworkConfigPathLocatorCommandInterface
{
    public function locate()
    {
        return null;
    }
}

/**
 * TooColdCommand Stub.
 */
class TooColdCommand extends BaseFrameworkConfigPathLocatorCommand implements FrameworkConfigPathLocatorCommandInterface
{
    public function locate()
    {
        return null;
    }
}

/**
 * JustRightCommand Stub.
 */
class JustRightCommand extends BaseFrameworkConfigPathLocatorCommand implements FrameworkConfigPathLocatorCommandInterface
{
    public function locate()
    {
        return 'path/to/config/directory';
    }
}


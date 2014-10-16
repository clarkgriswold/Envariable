<?php

namespace spec\Envariable;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Envariable\Config\FrameworkDetectionCommands\FrameworkDetectionCommandInterface;
use Envariable\Util\Filesystem;

/**
 * Envariable Config Loader Test.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class ConfigLoaderSpec extends ObjectBehavior
{
    /**
     * Test that the SUT is initializable.
     */
    function it_is_initializable()
    {
        $this->shouldHaveType('Envariable\ConfigLoader');
    }

    /**
     * Test that the loader returns an array with 'class' => 'JustRightCommand'.
     */
    function it_will_return_an_array_that_contains_class_justrightcommand()
    {
        $this->addCommand(new TooHotCommand());
        $this->addCommand(new TooColdCommand());
        $this->addCommand(new JustRightCommand());

        $this->loadConfigFile()->shouldReturn(array(
            'class' => 'JustRightCommand',
        ));
    }

    /**
     * Test that it will throw an exception with the message "Could not load Envariable config".
     */
    function it_will_throw_a_could_not_load_envariable_config_exception()
    {
        $this->addCommand(new TooHotCommand());
        $this->addCommand(new TooColdCommand());

        $this->shouldThrow(new \RuntimeException('Could not load Envariable config.'))->duringLoadConfigFile();
    }
}

/**
 * TooHotCommand Stub.
 */
class TooHotCommand implements FrameworkDetectionCommandInterface
{
    public function setFilesystem(Filesystem $filesystem) {}

    public function loadConfigFile()
    {
        return false;
    }
}

/**
 * TooColdCommand Stub.
 */
class TooColdCommand implements FrameworkDetectionCommandInterface
{
    public function setFilesystem(Filesystem $filesystem) {}

    public function loadConfigFile()
    {
        return false;
    }
}

/**
 * JustRightCommand Stub.
 */
class JustRightCommand implements FrameworkDetectionCommandInterface
{
    public function setFilesystem(Filesystem $filesystem) {}

    public function loadConfigFile()
    {
        return array(
            'class' => 'JustRightCommand',
        );
    }
}


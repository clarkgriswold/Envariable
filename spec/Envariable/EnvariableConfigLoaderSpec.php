<?php

namespace spec\Envariable;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Envariable\Config\FrameworkCommand\FrameworkCommandInterface;
use Envariable\Util\Filesystem;

class EnvariableConfigLoaderSpec extends ObjectBehavior
{
    /**
     * Test that the SUT is initializable.
     */
    function it_is_initializable()
    {
        $this->shouldHaveType('Envariable\EnvariableConfigLoader');
    }

    function it_will_return_an_array_that_contains_class_justrightcommand()
    {

        $this->addCommand(new TooHotCommand());
        $this->addCommand(new TooColdCommand());
        $this->addCommand(new JustRightCommand());

        $this->loadConfigFile()->shouldReturn(array(
            'class' => 'JustRightCommand',
        ));
    }

    function it_will_throw_a_could_not_load_envariable_config_exception()
    {

        $this->addCommand(new TooHotCommand());
        $this->addCommand(new TooColdCommand());

        $this->shouldThrow(new \Exception('Could not load Envariable config.'))->duringLoadConfigFile();
    }
}

class TooHotCommand implements FrameworkCommandInterface
{
    public function setFilesystem(Filesystem $filesystem) {}

    public function loadConfigFile()
    {
        return false;
    }
}

class TooColdCommand implements FrameworkCommandInterface
{
    public function setFilesystem(Filesystem $filesystem) {}

    public function loadConfigFile()
    {
        return false;
    }
}

class JustRightCommand implements FrameworkCommandInterface
{
    public function setFilesystem(Filesystem $filesystem) {}

    public function loadConfigFile()
    {
        return array(
            'class' => 'JustRightCommand',
        );
    }
}


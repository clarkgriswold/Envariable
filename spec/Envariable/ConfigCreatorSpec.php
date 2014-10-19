<?php

namespace spec\Envariable;

use Envariable\Util\Filesystem;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ConfigCreatorSpec extends ObjectBehavior
{
    /**
     * Test that the SUT is initializable.
     */
    function it_is_initializable()
    {
        $this->shouldHaveType('Envariable\ConfigCreator');
    }

    /**
     * Test that no exceptions are thrown.
     *
     * @param \Envariable\Util\Filesystem $filesystem
     */
    function it_throws_no_exceptions(Filesystem $filesystem)
    {
        $applicationConfigDirectoryPath = 'path/to/application/config';
        $envariableConfigDirectoryPath  = $applicationConfigDirectoryPath . '/Envariable';
        $configTemplatePath             = 'Config/ConfigTemplate.php';
        $envariableConfigFilePath       = $envariableConfigDirectoryPath . '/config.php';

        $filesystem
            ->createDirectory($envariableConfigDirectoryPath)
            ->willReturn(true);

        $filesystem
            ->copyFile($configTemplatePath, $envariableConfigFilePath)
            ->willReturn(true);

        $this->setFilesystem($filesystem);
        $this->createConfigFile($applicationConfigDirectoryPath);
    }

    /**
     * Test that it throws an exception as it fails to create the envariable config directory.
     *
     * @param \Envariable\Util\Filesystem $filesystem
     */
    function it_throws_exception_could_not_create_envariable_config_directory(Filesystem $filesystem)
    {
        $applicationConfigDirectoryPath = 'path/to/application/config';
        $envariableConfigDirectoryPath  = $applicationConfigDirectoryPath . '/Envariable';

        $filesystem
            ->createDirectory($envariableConfigDirectoryPath)
            ->willReturn(false);

        $this->setFilesystem($filesystem);
        $this->shouldThrow(new \RuntimeException('Could not create Envariable config directory.'))->duringCreateConfigFile($applicationConfigDirectoryPath);
    }

    /**
     * Test that it throws an exception as it fails to copy the config template file to its destination.
     *
     * @param \Envariable\Util\Filesystem $filesystem
     */
    function it_throws_exception_could_not_copy_config_file_to_destination(Filesystem $filesystem)
    {
        $applicationConfigDirectoryPath = 'path/to/application/config';
        $envariableConfigDirectoryPath  = $applicationConfigDirectoryPath . '/Envariable';
        $configTemplatePath             = 'Config/ConfigTemplate.php';
        $envariableConfigFilePath       = $envariableConfigDirectoryPath . '/config.php';

        $filesystem
            ->createDirectory($envariableConfigDirectoryPath)
            ->willReturn(true);

        $filesystem
            ->copyFile($configTemplatePath, $envariableConfigFilePath)
            ->willReturn(false);

        $this->setFilesystem($filesystem);
        $this->shouldThrow(new \RuntimeException('Could not copy config file to destination.'))->duringCreateConfigFile($applicationConfigDirectoryPath);
    }
}

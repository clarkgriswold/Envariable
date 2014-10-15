<?php

namespace spec\Envariable\Config\FrameworkDetectionCommands;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CodeIgniterDetectionCommandSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Envariable\Config\FrameworkDetectionCommands\CodeIgniterDetectionCommand');
    }

    /**
     * [it_does description]
     *
     * @param Envaraible\Util\Filesystem $filesystem
     */
    /*function it_does($filesystem)
    {
        $applicationRootDirectory = __DIR__ . '/CodeIgniter';

        $filesystem
            ->getApplicationRootPath($applicationRootDirectory)
            ->willReturn();



        $this->setFilesystem($filesystem);

        $this->loadConfigFile();
    }*/
}

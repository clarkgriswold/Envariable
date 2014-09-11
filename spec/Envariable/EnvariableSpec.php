<?php

namespace spec\Envariable;

use Envariable\CustomConfigProcessor;
use Envariable\Environment;
use Envariable\Util\ServerUtil;
use Envariable\Util\FileSystemUtil;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Envariable Test.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class EnvariableSpec extends ObjectBehavior
{
    /**
     * @var \Envaraible\Util\FileSystemUtil
     */
    private $fileSystemUtil;

    /**
     * Pre-test setup.
     *
     * @param \Envariable\CustomConfigProcessor $customConfigProcessor
     * @param \Envariable\Environment           $environment
     * @param \Envariable\Util\ServerUtil       $serverUtil
     * @param \Envariable\Util\FileSystemUtil   $fileSystemUtil
     */
    function let(
        CustomConfigProcessor $customConfigProcessor,
        Environment $environment,
        ServerUtil $serverUtil,
        FileSystemUtil $fileSystemUtil
    ) {
        $customConfigProcessor->beADoubleOf('Envariable\CustomConfigProcessor');
        $environment->beADoubleOf('Envariable\Environment');
        $serverUtil->beADoubleOf('Envariable\Util\ServerUtil');
        $fileSystemUtil->beADoubleOf('Envariable\Util\FileSystemUtil');

        $this->fileSystemUtil = $fileSystemUtil;

        $this->beConstructedWith(
            $customConfigProcessor,
            $environment,
            $serverUtil,
            $this->fileSystemUtil
        );
        $this->shouldHaveType('Envariable\Envariable');
    }

    function it_does_stuff(Environment $environment)
    {
        //$this->fileSystemUtil->getApplicationRootPath()->willReturn('ding');
        $this->getEnvironment()->shouldReturn($environment);
    }

    /*function it_does_other_stuff()
    {
        $this->fileSystemUtil->getApplicationRootPath()->willReturn('dong');
    }*/
}

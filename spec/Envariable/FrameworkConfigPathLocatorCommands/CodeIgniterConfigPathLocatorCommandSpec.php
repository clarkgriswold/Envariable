<?php

namespace spec\Envariable\FrameworkConfigPathLocatorCommands;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * CodeIgniter Config Path Locator Command Test.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class CodeIgniterConfigPathLocatorCommandSpec extends ObjectBehavior
{
    /**
     * Test that the SUT is initializable.
     */
    function it_is_initializable()
    {
        $this->shouldHaveType('Envariable\FrameworkConfigPathLocatorCommands\CodeIgniterConfigPathLocatorCommand');
    }

    /**
     * [it_does description]
     */
    /*function it_does()
    {

    }*/
}

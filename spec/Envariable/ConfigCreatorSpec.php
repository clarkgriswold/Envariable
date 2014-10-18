<?php

namespace spec\Envariable;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ConfigCreatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Envariable\ConfigCreator');
    }
}

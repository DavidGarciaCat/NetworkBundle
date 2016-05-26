<?php

namespace spec\DavidGarciaCat\NetworkBundle\URL;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class URLSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('DavidGarciaCat\NetworkBundle\URL\URL');
    }
}

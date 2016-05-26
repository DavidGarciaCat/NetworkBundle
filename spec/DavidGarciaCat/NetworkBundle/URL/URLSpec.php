<?php

namespace spec\DavidGarciaCat\NetworkBundle\URL;

use DavidGarciaCat\NetworkBundle\URL\URLInterface;
use PhpSpec\ObjectBehavior;

class URLSpec extends ObjectBehavior
{
    function it_should_implement_URLInterface()
    {
        $this->shouldImplement(URLInterface::class);
    }
}

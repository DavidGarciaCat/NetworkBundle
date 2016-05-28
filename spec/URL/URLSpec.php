<?php

namespace spec\DavidGarciaCat\NetworkPolyfill\URL;

use DavidGarciaCat\NetworkPolyfill\URL\URLInterface;
use PhpSpec\ObjectBehavior;

class URLSpec extends ObjectBehavior
{
    function it_should_implements_URLInterface()
    {
        $this->shouldImplement(URLInterface::class);
    }
}

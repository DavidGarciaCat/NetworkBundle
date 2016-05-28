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

    function it_should_be_able_to_parse_a_given_URL()
    {
        $this->parseUrl('this-is-my-awesome-url')->shouldReturn($this);
    }

    function it_exposes_the_protocol()
    {
        $this->setProtocol('HTTP')->shouldReturn($this);
        $this->getProtocol()->shouldReturn('http');
        $this->getPort()->shouldReturn(80);
    }

    function it_exposes_the_protocol_when_uses_non_standard_port()
    {
        $this->setProtocol('HTTP', 8080)->shouldReturn($this);
        $this->getProtocol()->shouldReturn('http');
        $this->getPort()->shouldReturn(8080);
    }

    function it_exposes_the_username()
    {
        $this->setUsername('username')->shouldReturn($this);
        $this->getUsername()->shouldReturn('username');
    }

    function it_exposes_the_password()
    {
        $this->setPassword('password')->shouldReturn($this);
        $this->getPassword()->shouldReturn('password');
    }

    function it_exposes_the_host_name()
    {
        $this->setHost('host')->shouldReturn($this);
        $this->getHost()->shouldReturn('host');
    }

    function it_exposes_the_port()
    {
        $this->setPort(8443)->shouldReturn($this);
        $this->getPort()->shouldReturn(8443);
    }

    function it_exposes_the_path()
    {
        $this->setPath('/path/to/url/resource')->shouldReturn($this);
        $this->getPath()->shouldReturn('/path/to/url/resource');
    }

    function it_exposes_the_query_string_as_array()
    {
        $this->setQueryString(['key' => 'value'])->shouldReturn($this);
        $queryString = $this->getQueryString();
        $queryString->shouldBeArray();
        $queryString->shouldHaveCount(1);
        $queryString->shouldHaveKeyWithValue('key', 'value');
    }

    function it_adds_an_extra_key_with_encoded_value_to_the_query_string()
    {
        $this->setQueryString(['key' => 'value'])->shouldReturn($this);
        $this->addQueryString('new_key', 'another value', true);

        $queryString = $this->getQueryString();
        $queryString->shouldBeArray();
        $queryString->shouldHaveCount(2);
        $queryString->shouldHaveKeyWithValue('key', 'value');
        $queryString->shouldHaveKeyWithValue('new_key', 'another value');
    }

    function it_adds_an_extra_key_with_non_encoded_value_to_the_query_string()
    {
        $this->setQueryString(['key' => 'value'])->shouldReturn($this);
        $this->addQueryString('new_key', 'another value');

        $queryString = $this->getQueryString();
        $queryString->shouldBeArray();
        $queryString->shouldHaveCount(2);
        $queryString->shouldHaveKeyWithValue('key', 'value');
        $queryString->shouldHaveKeyWithValue('new_key', 'another%20value');
    }

    function it_removes_an_existing_key_of_the_query_string()
    {
        $this->setQueryString(['key' => 'value'])->shouldReturn($this);
        $this->addQueryString('new_key', 'another value', true);
        $this->removeQueryString('key')->shouldReturn($this);

        $queryString = $this->getQueryString();
        $queryString->shouldBeArray();
        $queryString->shouldHaveCount(1);
        $queryString->shouldHaveKeyWithValue('new_key', 'another value');
    }

    function it_cannot_remove_a_non_existing_key_of_the_query_string()
    {
        $this->setQueryString(['key' => 'value'])->shouldReturn($this);
        $this->removeQueryString('wrong_key')->shouldReturn($this);

        $queryString = $this->getQueryString();
        $queryString->shouldBeArray();
        $queryString->shouldHaveCount(1);
        $queryString->shouldHaveKeyWithValue('key', 'value');
    }

    function it_exposes_the_anchor()
    {
        $this->setAnchor('anchor')->shouldReturn($this);
        $this->getAnchor()->shouldReturn('anchor');
    }

    function it_exposes_the_port_for_HTTP_protocol()
    {
        $this->getStandardPort('HTTP')->shouldReturn(80);
    }

    function it_exposes_the_port_for_HTTPS_protocol()
    {
        $this->getStandardPort('HTTPS')->shouldReturn(443);
    }

    function it_exposes_the_port_for_FTP_protocol()
    {
        $this->getStandardPort('FTP')->shouldReturn(21);
    }

    function it_exposes_the_port_for_IMAP_protocol()
    {
        $this->getStandardPort('IMAP')->shouldReturn(143);
    }

    function it_exposes_the_port_for_IMAPS_protocol()
    {
        $this->getStandardPort('IMAPS')->shouldReturn(993);
    }

    function it_exposes_the_port_for_POP3_protocol()
    {
        $this->getStandardPort('POP3')->shouldReturn(110);
    }

    function it_exposes_the_port_for_POP3S_protocol()
    {
        $this->getStandardPort('POP3S')->shouldReturn(995);
    }

    function it_cannot_expost_the_port_for_non_standard_protocol()
    {
        $this->getStandardPort('UNKNOWN')->shouldReturn(null);
    }

    function it_exposes_a_simple_URL_absolute_path()
    {
        $this->resolvePath('/foo/bar/../boo.php')->shouldReturn('/foo/boo.php');
    }

    function it_exposes_a_complex_URL_absolute_path()
    {
        $this->resolvePath('/foo/bar/../../boo.php')->shouldReturn('/boo.php');
    }

    function it_exposes_a_more_complex_URL_absolute_path()
    {
        $this->resolvePath('/foo/bar/.././/boo.php')->shouldReturn('/foo/boo.php');
    }

    function it_exposes_a_even_more_complex_URL_absolute_path()
    {
        $this->resolvePath('/../..//foo/bar/.//boo.php')->shouldReturn('/foo/bar/boo.php');
    }

    function it_parses_a_simple_query_string()
    {
        $queryString = $this->parseRawQueryString('key1=value1&key2=value2');
        $queryString->shouldBeArray();
        $queryString->shouldHaveCount(2);
        $queryString->shouldHaveKeyWithValue('key1', 'value1');
        $queryString->shouldHaveKeyWithValue('key2', 'value2');
    }

    function it_parses_a_complex_query_string()
    {
        $queryString = $this->parseRawQueryString('key1[key2]=value1&key1[key3]=value2');
        $queryString->shouldBeArray();
        $queryString->shouldHaveCount(1);
        $queryString->shouldHaveKey('key1');
        $queryString['key1']->shouldBeArray();
        $queryString['key1']->shouldHaveCount(2);
        $queryString['key1']->shouldHaveKeyWithValue('key2', 'value1');
        $queryString['key1']->shouldHaveKeyWithValue('key3', 'value2');
    }

    function it_exposes_the_provided_URL()
    {
        $url = 'http://username:password@hostname:9090/path?arg=value#anchor';

        $this->setUrl($url)->shouldReturn($this);
        $this->parseUrl($url)->shouldReturn($this);

        $parseQueryString = $this->parseRawQueryString('arg=value');
        $parseQueryString->shouldBeArray();
        $parseQueryString->shouldHaveCount(1);
        $parseQueryString->shouldHaveKeyWithValue('arg', 'value');

        $this->setQueryString(['arg' => 'value'])->shouldReturn($this);
        $this->getRawQueryString()->shouldReturn('arg=value');
        $this->getStandardPort('http')->shouldReturn(80);
        $this->getUrl()->shouldReturn($url);
    }

    function it_cannot_expose_the_url_if_were_not_provided()
    {
        $this->getUrl()->shouldReturn(null);
    }

    function it_exposes_a_parsed_url_via_constructor()
    {
        $url = 'http://username:password@hostname:9090/path?arg=value#anchor';

        $this->beConstructedWith($url);

        $this->getUrl()->shouldReturn($url);
    }
}

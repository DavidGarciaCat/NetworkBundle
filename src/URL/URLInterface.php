<?php

/**
 * Interface for URL class.
 *
 * @author David Garcia <me@davidgarcia.cat>
 */
namespace DavidGarciaCat\NetworkPolyfill\URL;

/**
 * Interface URLInterface.
 */
interface URLInterface
{
    /**
     * Parse the provided URL using the byDefault() and handleUrl() methods.
     *
     * @param string $url
     *
     * @return URLInterface
     */
    public function parseUrl($url);

    /**
     * Sets the provided URL.
     *
     * @param string $url
     *
     * @return URLInterface
     */
    public function setUrl($url);

    /**
     * Gets the raw URL. It is created automatically using the current details
     * that has been parsed previously via parseUrl() method.
     *
     * @return string
     */
    public function getUrl();

    /**
     * Sets the Scheme and Port.
     * If Port is not provided, the "by default" port will be set.
     *
     * @param string   $protocol
     * @param int|null $port
     *
     * @return URLInterface
     */
    public function setProtocol($protocol, $port = null);

    /**
     * Gets the Scheme.
     *
     * @return string
     */
    public function getProtocol();

    /**
     * Sets the user name.
     *
     * @param string $username
     *
     * @return URLInterface
     */
    public function setUsername($username);

    /**
     * Gets the user name.
     *
     * @return string
     */
    public function getUsername();

    /**
     * Sets the password.
     *
     * @param string $password
     *
     * @return URLInterface
     */
    public function setPassword($password);

    /**
     * Gets the password.
     *
     * @return string
     */
    public function getPassword();

    /**
     * Sets the host name (AKA domain).
     *
     * @param string $host
     *
     * @return URLInterface
     */
    public function setHost($host);

    /**
     * Gets the host name (AKA domain).
     *
     * @return string
     */
    public function getHost();

    /**
     * Sets the port.
     *
     * @param int $port
     *
     * @return URLInterface
     */
    public function setPort($port);

    /**
     * Gets the port.
     *
     * @return int
     */
    public function getPort();

    /**
     * Sets the path.
     *
     * @param string $path
     *
     * @return URLInterface
     */
    public function setPath($path);

    /**
     * Gets the path.
     *
     * @return string
     */
    public function getPath();

    /**
     * Sets an array of arguments as query string.
     *
     * @param array $queryString
     *
     * @return URLInterface
     */
    public function setQueryString(array $queryString);

    /**
     * Adds a new key/value as query string. If the provided key exists will be overwritten.
     * (Optional) Handle the provided value as a pre-encoded value (FALSE by default).
     *
     * @param string $key
     * @param string $value
     * @param bool   $preEncoded
     *
     * @return URLInterface
     */
    public function addQueryString($key, $value, $preEncoded = false);

    /**
     * Removes the provided key from the query string.
     *
     * @param string $key
     *
     * @return URLInterface
     */
    public function removeQueryString($key);

    /**
     * Gets the query string as an array.
     *
     * @return array
     */
    public function getQueryString();

    /**
     * Gets the query string as a string.
     *
     * @return string
     */
    public function getRawQueryString();

    /**
     * Sets the URL anchor.
     *
     * @param string $anchor
     *
     * @return URLInterface
     */
    public function setAnchor($anchor);

    /**
     * Gets the URL anchor.
     *
     * @return string
     */
    public function getAnchor();

    /**
     * Checks the provided URL format and sets the by-default values.
     *
     * @param string $url
     *
     * @return URLInterface
     */
    public function byDefault($url);

    /**
     * Sets all available values given the provided URL.
     *
     * @param string $url
     *
     * @return URLInterface
     */
    public function handleUrl($url);

    /**
     * Provides the standard port for the provided scheme.
     *
     * @param string $scheme
     *
     * @return int
     */
    public function getStandardPort($scheme);

    /**
     * Parse the provided query string (as string) and returns an array.
     *
     * @param string $queryString
     *
     * @return array
     */
    public function parseRawQueryString($queryString);

    /**
     * Resolves //, ../ and ./ from a path and returns.
     * This method can also be called statically.
     *
     * @param string $path
     *
     * @return string
     */
    public function resolvePath($path);
}

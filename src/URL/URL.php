<?php

/**
 * URL class that handles the provided URL to be used as an object.
 *
 * @author David Garcia <me@davidgarcia.cat>
 *
 * @copyright 2016 David Garcia
 *
 * @license https://opensource.org/licenses/MIT MIT
 *
 * @version 1.0.0
 */

namespace DavidGarciaCat\NetworkPolyfill\URL;

/**
 * Class URL.
 */
class URL implements URLInterface
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $protocol;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port = 80;

    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $queryString = array();

    /**
     * @var string
     */
    private $anchor;

    /**
     * URL constructor.
     *
     * @param string|null $url
     */
    public function __construct($url = null)
    {
        if (!empty($url) && is_string($url)) {
            $this->parseUrl($url);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function parseUrl($url)
    {
        $this->setUrl($url);
        $this->byDefault($url);
        $this->handleUrl($url);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        if (empty($this->url)) {
            return null;
        }

        $queryString = $this->getRawQueryString();

        $url = $this->protocol.'://';
        $url .= $this->username.(!empty($this->password) ? ':' : '');
        $url .= $this->password.(!empty($this->username) ? '@' : '');
        $url .= $this->host;
        $url .= $this->port === $this->getStandardPort($this->protocol) ? '' : ':'.$this->port;
        $url .= $this->path;
        $url .= !empty($queryString) ? '?'.$queryString : '';
        $url .= !empty($this->anchor) ? '#'.$this->anchor : '';

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function setProtocol($protocol, $port = null)
    {
        $port = (!is_null($port) && is_integer($port)) ?
            $port :
            $this->getStandardPort($protocol);

        $this->protocol = strtolower($protocol);
        $this->port = $port;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * {@inheritdoc}
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function setQueryString(array $queryString)
    {
        $this->queryString = $queryString;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addQueryString($key, $value, $preEncoded = false)
    {
        if ($preEncoded) {
            $this->queryString[$key] = $value;
        } else {
            $this->queryString[$key] = is_array($value) ? array_map('rawurlencode', $value): rawurlencode($value);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeQueryString($key)
    {
        if (isset($this->queryString[$key])) {
            unset($this->queryString[$key]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryString()
    {
        return $this->queryString;
    }

    /**
     * {@inheritdoc}
     */
    public function getRawQueryString()
    {
        return http_build_query($this->queryString);
    }

    /**
     * {@inheritdoc}
     */
    public function setAnchor($anchor)
    {
        $this->anchor = $anchor;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAnchor()
    {
        return $this->anchor;
    }

    /**
     * {@inheritdoc}
     */
    public function byDefault($url)
    {
        if (!preg_match('/^[a-z0-9]+:\/\//i', $url)) {
            $protocol = (!isset($_SERVER['HTTPS']) || 'on' !== strtolower($_SERVER['HTTPS'])) ? 'http' : 'https';
            $this->protocol = $protocol;

            $host = (isset($_SERVER['HTTP_HOST']) && preg_match('/^(.*)(:([0-9]+))?$/U', $_SERVER['HTTP_HOST'], $matches)) ?
                (isset($matches[1]) && !empty($matches[1]) ? $matches[1] : null) : null;

            $port = (isset($_SERVER['HTTP_HOST']) && preg_match('/^(.*)(:([0-9]+))?$/U', $_SERVER['HTTP_HOST'], $matches)) ?
                (isset($matches[3]) && !empty($matches[3]) ? $matches[3] : $this->getStandardPort($protocol)) : null;

            $host = !empty($host) ? $host : (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost');
            $port = !empty($port) ? $port : (isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : $this->getStandardPort($protocol));
            $path = (isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '/');
            $queryString = isset($_SERVER['QUERY_STRING']) ? $this->parseRawQueryString($_SERVER['QUERY_STRING']) : array();

            $this->host = $host;
            $this->port = $port;
            $this->path = $path;
            $this->queryString = $queryString;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function handleUrl($url)
    {
        if (!empty($url)) {
            $urlInfo = parse_url($url);

            foreach ($urlInfo as $key => $value) {
                switch ($key) {
                    case 'scheme':
                        $this->protocol = $value;
                        $this->port = $this->getStandardPort($value);
                        break;
                    case 'user':
                        $this->username = $value;
                        break;
                    case 'pass':
                        $this->password = $value;
                        break;
                    case 'host':
                        $this->host = $value;
                        break;
                    case 'port':
                        $this->port = $value;
                        break;
                    case 'path':
                        if ('/' === $value{0}) {
                            $this->path = $value;
                        } else {
                            $path = DIRECTORY_SEPARATOR === dirname($this->path) ? '' : dirname($this->path);
                            $this->path = sprintf('%s/%s', $path, $value);
                        }
                        break;
                    case 'query':
                        $this->queryString = $this->parseRawQueryString($value);
                        break;
                    case 'fragment':
                        $this->anchor = $value;
                        break;
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getStandardPort($scheme)
    {
        switch (strtolower(trim($scheme))) {
            case 'http':
                return 80;
            case 'https':
                return 443;
            case 'ftp':
                return 21;
            case 'imap':
                return 143;
            case 'imaps':
                return 993;
            case 'pop3':
                return 110;
            case 'pop3s':
                return 995;
            default:
                return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function parseRawQueryString($queryString)
    {
        parse_str($queryString, $parts);

        return $parts;
    }

    /**
     * {@inheritdoc}
     */
    public function resolvePath($path)
    {
        $path = explode('/', str_replace('//', '/', $path));

        $countPath = count($path);

        for ($i = 0; $i < $countPath; $i++) {
            if ('.' === $path[$i]) {
                unset($path[$i]);
                $path = array_values($path);
                $i--;
                $countPath = count($path);
            } elseif ('..' === $path[$i] && ($i > 1 || ($i == 1 && '' !== $path[0]))) {
                unset($path[$i]);
                unset($path[$i-1]);
                $path = array_values($path);
                $i -= 2;
                $countPath = count($path);
            } elseif ('..' === $path[$i] && 1 === $i && '' === $path[0]) {
                unset($path[$i]);
                $path = array_values($path);
                $i--;
                $countPath = count($path);
            } else {
                continue;
            }
        }

        return implode('/', $path);
    }
}

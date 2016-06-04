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

namespace DavidGarciaCat\NetworkPolyfill\DNSBL;

use IPTools\Network;

/**
 * Class DNSBL.
 */
class DNSBL
{
    const DNSBL_BARRACUDA_CENTRAL = 'b.barracudacentral.org';
    const DNSBL_SPAM_CANNIBAL = 'bl.spamcannibal.org';
    const DNSBL_SPAM_COP = 'bl.spamcop.net';
    const DNSBL_SPAMHAUS = 'zen.spamhaus.org';
    const DNSBL_SORBS = 'dnsbl.sorbs.net';

    /**
     * Contains all black lists we are going to use in otder to check the provided domain or IP Address.
     *
     * @var array
     */
    private $blacklists = [];

    /**
     * Contains all A DNS records to be checked by all blacklists.
     *
     * @var array
     */
    private $hosts = [];

    /**
     * Contains all details we may need after check each host for each blacklist. Format:
     * [
     *     "ip_address" => [
     *         "dns" => "Reverse IP Address DNS Name",
     *         "blacklisted" => true,
     *         "blacklists" => [
     *             "blacklist_1" => false,
     *             "blacklist_2" => false,
     *             "blacklist_3" => true,
     *         ],
     *     ],
     *     ...
     * ]
     * A new associative array with all those details will be provided for each Host.
     *
     * @var array
     */
    private $results = [];

    /**
     * Counter to know how many checks we have processed.
     *
     * @var int
     */
    private $totalChecks = 0;

    /**
     * Counter to know how many times we have found an IP Address on blacklist services.
     *
     * @var int
     */
    private $totalBlacklists = 0;

    /**
     * DNSBL constructor.
     */
    public function __construct()
    {
        $this->blacklists = [
            self::DNSBL_BARRACUDA_CENTRAL,
            self::DNSBL_SPAM_CANNIBAL,
            self::DNSBL_SPAM_COP,
            self::DNSBL_SPAMHAUS,
            self::DNSBL_SORBS,
        ];

        sort($this->blacklists);
    }

    /**
     * This method adds a new Black List to be used.
     *
     * @param string $blacklist
     *
     * @throws \InvalidArgumentException
     *
     * @return DNSBL
     */
    public function addBlacklist($blacklist)
    {
        if (!is_string($blacklist)) {
            throw new \InvalidArgumentException('Provided `blacklist` must be a String');
        }

        if (!checkdnsrr($blacklist, 'ANY')) {
            throw new \InvalidArgumentException('Provided `blacklist` must be a valid DNS');
        }

        if (!in_array($blacklist, $this->blacklists)) {
            $this->blacklists[] = $blacklist;

            sort($this->blacklists);
        }

        return $this;
    }

    /**
     * This method removes an existing Black List from the list to be used.
     *
     * @param string $blacklist
     *
     * @throws \InvalidArgumentException
     *
     * @return DNSBL
     */
    public function removeBlacklist($blacklist)
    {
        if (!is_string($blacklist)) {
            throw new \InvalidArgumentException('Provided `blacklist` must be a String');
        }

        if (!checkdnsrr($blacklist, 'ANY')) {
            throw new \InvalidArgumentException('Provided `blacklist` must be a valid DNS');
        }

        if ($key = array_search($blacklist, $this->blacklists)) {
            unset($this->blacklists[$key]);

            sort($this->blacklists);
        }

        return $this;
    }

    /**
     * It sets an array of blacklists to be used.
     *
     * @param array $blacklists
     */
    public function setBlacklists(array $blacklists)
    {
        foreach ($blacklists as $blacklist) {
            $this->addBlacklist($blacklist);
        }
    }

    /**
     * It returns an array with all blacklists to be used.
     *
     * @return array
     */
    public function getBlacklists()
    {
        return $this->blacklists;
    }

    /**
     * Return the number of hosts that will be checked for each blacklist.
     *
     * @return int
     */
    public function countHosts()
    {
        return count($this->hosts);
    }

    /**
     * Return all hosts that will be checked for each blacklist.
     *
     * @return array
     */
    public function getHosts()
    {
        return $this->hosts;
    }

    /**
     * Given a domain, check all A & MX & SPF records in order to get all IP Addresses.
     *
     * @param $domain
     *
     * @return DNSBL
     */
    public function parseDnsRecords($domain)
    {
        $this->parseADnsRecords($domain);
        $this->parseMxDnsRecords($domain);
        $this->parseSpfDnsRecords($domain);

        return $this;
    }

    /**
     * Given a domain, check all A records in order to get all IP Addresses.
     *
     * @param $domain
     *
     * @return DNSBL
     */
    public function parseADnsRecords($domain)
    {
        if (!is_string($domain)) {
            throw new \InvalidArgumentException('Provided `domain` must be a String');
        }

        if ($this->isIpAddress($domain)) {
            $this->hosts[] = $domain;
        } else {
            if (!checkdnsrr($domain, 'ANY')) {
                throw new \InvalidArgumentException('Provided `domain` must be a valid DNS');
            }

            $aRecords = dns_get_record($domain, DNS_A);

            foreach ($aRecords as $dnsRecord) {
                if (key_exists('ip', $dnsRecord) && !in_array($dnsRecord['ip'], $this->hosts)) {
                    $this->hosts[] = $dnsRecord['ip'];
                }
            }
        }

        sort($this->hosts);

        return $this;
    }

    /**
     * Given a domain, check all MX records in order to get all IP Addresses.
     *
     * @param $domain
     *
     * @return DNSBL
     */
    public function parseMxDnsRecords($domain)
    {
        if (!is_string($domain)) {
            throw new \InvalidArgumentException('Provided `domain` must be a String');
        }

        if ($this->isIpAddress($domain)) {
            $this->hosts[] = $domain;
        } else {
            if (!checkdnsrr($domain, 'ANY')) {
                throw new \InvalidArgumentException('Provided `domain` must be a valid DNS');
            }

            $mxRecords = dns_get_record($domain, DNS_MX);

            foreach ($mxRecords as $dnsRecord) {
                if (key_exists('host', $dnsRecord)) {
                    $this->parseADnsRecords($dnsRecord['host']);
                }
            }
        }

        sort($this->hosts);

        return $this;
    }

    /**
     * Given a domain, check all SPF records in order to get all IP Addresses.
     *
     * @param $domain
     *
     * @return DNSBL
     */
    public function parseSpfDnsRecords($domain)
    {
        if (!is_string($domain)) {
            throw new \InvalidArgumentException('Provided `domain` must be a String');
        }

        if ($this->isIpAddress($domain)) {
            $this->hosts[] = $domain;
        } else {
            if (!checkdnsrr($domain, 'ANY')) {
                throw new \InvalidArgumentException('Provided `domain` must be a valid DNS');
            }

            $txtRecords = dns_get_record($domain, DNS_TXT);

            foreach ($txtRecords as $dnsRecord) {
                if (key_exists('txt', $dnsRecord) && preg_match('/spf1/', $dnsRecord['txt'])) {
                    $dnsRecord = preg_replace('/\s+/', ' ', $dnsRecord['txt']);
                    $explodes = explode(' ', $dnsRecord);

                    foreach ($explodes as $explode) {
                        if (preg_match('/include\:(.+)/', $explode)) {
                            $include = explode(':', $explode)[1];

                            $this->parseSpfDnsRecords($include);
                        }

                        if (preg_match('/ip4\:(.+)/', $explode)) {
                            $ip4 = explode(':', $explode)[1];
                            $hostMask = explode('/', $ip4);

                            $host = isset($hostMask[0]) ? $hostMask[0] : null;
                            $mask = isset($hostMask[1]) ? $hostMask[1] : (isset($hostMask[0]) ? 32 : null);

                            $network = Network::parse($host.'/'.$mask);

                            foreach ($network as $ip) {
                                $this->parseADnsRecords((string) $ip);
                            }
                        }
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Check all hosts for each provided blacklist service.
     *
     * @return DNSBL
     */
    public function runBlacklistChecks()
    {
        $dnsResolver = new \Net_DNS2_Resolver();

        foreach ($this->hosts as $ipAddress) {
            $this->results[$ipAddress] = [
                'dns' => gethostbyaddr($ipAddress),
                'blacklisted' => false,
                'blacklists' => [],
            ];

            foreach ($this->blacklists as $blacklist) {
                $reverseIp = $this->reverseIp($ipAddress);

                $blackListAddress = $reverseIp.'.'.$blacklist;

                try {
                    $this->totalChecks += 1;

                    $response = $dnsResolver->query($blackListAddress);

                    if ($response) {
                        $this->totalBlacklists += 1;

                        $this->results[$ipAddress]['blacklisted'] = true;
                        $this->results[$ipAddress]['blacklists'][$blacklist] = true;
                    } else {
                        $this->results[$ipAddress]['blacklists'][$blacklist] = false;
                    }
                } catch (\Net_DNS2_Exception $exception) {
                    // The \Net_DNS2_Resolver->query() may throw a \Net_DNS2_Exception Exception
                    // when we get no response from blacklist:
                    // "DNS request failed: The domain name referenced in the query does not exist."
                    $this->results[$ipAddress]['blacklists'][$blacklist] = false;
                }
            }
        }

        echo json_encode($this->results) . PHP_EOL . PHP_EOL;

        return $this;
    }

    /**
     * Returns an associative array with all information we have got checking all ip addresses.
     *
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * Returns the number of checks that we have processed.
     *
     * @return int
     */
    public function getTotalChecks()
    {
        return $this->totalChecks;
    }

    /**
     * Returns the number of times we have found an IP Address blacklisted.
     *
     * @return int
     */
    public function getTotalBlacklists()
    {
        return $this->totalBlacklists;
    }

    /**
     * Returns the percentage of options to be flagged as SPAM by the recipient's Mail Server.
     *
     * @return float
     */
    public function getBlacklistPercentage()
    {
        return $this->totalBlacklists / $this->totalChecks * 100;
    }

    /**
     * Checks if the provided string is a valid IPv4 Address.
     *
     * @param string $ipAddress
     *
     * @return bool
     */
    private function isIpAddress($ipAddress)
    {
        if (empty($ipAddress)) {
            return false;
        }

        $parts = explode('.', $ipAddress);

        if (count($parts) !== 4) {
            return false;
        }

        foreach ($parts as $part) {
            if (!is_numeric($part)) {
                return false;
            }

            if ($part < 0 || $part > 255) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if the provided string is a valid IP address: if True reverse the IP Address.
     * So 192.168.0.1 will be returned as 1.0.168.192 given this is the format that blacklist services are using.
     *
     * @param string $ipAddress
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    private function reverseIp($ipAddress)
    {
        if ($this->isIpAddress($ipAddress)) {
            return implode('.', array_reverse(explode('.', $ipAddress)));
        }

        throw new \InvalidArgumentException('Provided `string` is not a valid IPv4 Address');
    }
}

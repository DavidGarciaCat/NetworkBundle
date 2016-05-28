# DavidGarciaCat / Network Polyfill

This is a set of reusable network tools, based on PEAR PHP extensions, but written using pure PHP code, so all developers will be able to use these features when the web server is not providing them.

[![Build Status](https://travis-ci.org/DavidGarciaCat/network-polyfill.svg?branch=master)](https://travis-ci.org/DavidGarciaCat/network-polyfill)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/f86840c9-b589-40d0-9fe9-7705082b34f0/mini.png)](https://insight.sensiolabs.com/projects/f86840c9-b589-40d0-9fe9-7705082b34f0)
![PHP 5.5](https://img.shields.io/badge/PHP-5.5-8892bf.svg)
![PHP 5.5](https://img.shields.io/badge/PHP-5.6-8892bf.svg)
![PHP 5.5](https://img.shields.io/badge/PHP-7.0-8892bf.svg)

## Change Log

### v1.0.0

**Net URL**

Parse a given URL to be managed as an object

## Coming soon

**Check IP**

Easy way to check, verify and validate the given IP Address

**DNS**

DNS Resolver to check the given domain using different Name Servers

**DNS Black List**

Check if the given domain is black listed. This process will check (recursivelly) all IP Addresses for the given domain:
- A record
- MX records
- SPF included domains
- SPF included IP Addresses

**GeoIP**

IP GeoLocation service that will use a local database (provided from MaxMind).  
Please note these databases may geolocate the Country easily, but some times the city may be wrong.

**PING**

Ping the given domain to know if the server is responding.

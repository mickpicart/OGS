<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;
use App\Mail\ErrorAlert;
use Illuminate\Support\Facades\Mail;

class DataHelper
{
    /**
    * Randomly generate a password
    *
    * @param int $length
    * @param int $count
    * @param string $character
    * @return string
    */

    public static function randomPassword(int $length, int $count, string $characters)
    {
        // $length - the length of the generated password
        // $count - number of passwords to be generated
        // $characters - types of characters to be used in the password

        // Define variables used within the function
        $symbols = array();
        $used_symbols = '';
        $pass = '';

        // An array of different character types
        $symbols["lower_case"] = 'abcdefghijklmnopqrstuvwxyz';
        $symbols["upper_case"] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $symbols["numbers"] = '1234567890';
        $symbols["special_symbols"] = '!?~@#-_+<>[]{}';

        // Get characters types to be used for the passsword
        $characters = explode(",", $characters);
        foreach ($characters as $value) {
            // Build a string with all characters
            $used_symbols .= $symbols[$value];
        }
        // strlen starts from 0 so to get number of characters deduct 1
        $symbols_length = strlen($used_symbols) - 1;

        for ($p = 0; $p < $count; $p++) {
            $pass = '';
            for ($i = 0; $i < $length; $i++) {
                // Get a random character from the string with all characters
                $n = rand(0, $symbols_length);
                // Add the character to the password string
                $pass .= $used_symbols[$n];
            }
        }
        // return the generated password
        return $pass;
    }


    /**
    * Check which CMS a website is built with
    *
    * @param GuzzleHttp\Client $website
    * @return string
    */

    public static function whichCms(object $website)
    {
        // 'http_errors' set to false to disable throwing exceptions on an HTTP protocol errors
        $client = new Client(['http_errors' => false]);

        // Two conditions checked : if /wp-admin or /wp-mail.php exists, it's a Wordpress built site, if not
        // it's a Prestashop as only two CMS have been used by BCD dev and mainly Wordpress
        if ($client->request('GET', $website->url . '/wp-admin', ['verify'  => true])->getStatusCode() === 200) {
            return 'WordPress';
        } elseif ($client->request('GET', $website->url . '/wp-mail.php', ['verify'  => true])->getStatusCode() === 200) {
            return 'WordPress';
        } else {
            return 'Not WordPress';
        }
    }


    /**
    * Get main domain from an url
    *
    * @param string $url
    * @return string
    */

    public static function getDomainFromUrl(string $url)
    {
        $pieces = parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : $pieces['path'];
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            return $regs['domain'];
        }
        return false;
    }


    /**
    * Check Website SSL Certificate
    *
    * @param string $domain
    * @return bool
    */

    public static function isSslValid(string $domain)
    {
        $res = false;
        $stream = @stream_context_create(array( 'ssl' => array( 'capture_peer_cert' => true ) ));
        $socket = @stream_socket_client('ssl://' . $domain . ':443', $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $stream);

        // If we got a ssl certificate we check here, if the certificate domain
        // matches the website domain.
        if ($socket) {
            $cont = stream_context_get_params($socket);
            $cert_ressource = $cont['options']['ssl']['peer_certificate'];
            $cert = openssl_x509_parse($cert_ressource);

            // Expected name has format "/CN=*.yourdomain.com"
            $namepart = explode('=', $cert['name']);

            // We want to correctly confirm the certificate even
            // for subdomains like "www.yourdomain.com"
            if (count($namepart) == 2) {
                $cert_domain  = trim($namepart[1], '*. ');
                $check_domain = substr($domain, -strlen($cert_domain));
                $res = ($cert_domain == $check_domain);
            }
        }
        return $res;
    }


    /**
    * Check wether robots.txt exists for a given website
    *
    * @param object $website
    * @return string
    */

    public static function getRobotsTxt(object $website)
    {
        // 'http_errors' set to false to disable throwing exceptions on an HTTP protocol errors
        $client = new Client(['http_errors' => false]);

        $url = $website->url;
        // Send request with SSL validation : 'verify' => false and with /robots.txt extension
        $response = $client->request('GET', $url . '/robots.txt', ['verify'  => true]);

        if ($response->getStatusCode() == 200) {
            $response = $response->getBody();

            $validator = Validator::make(['response' => $response], [
                'response' => 'required|starts_with:User-agent:'
            ]);
            // Return null if $response is not a robots.txt file
            if ($validator->fails()) {
                return null;
            } else {
                return $response;
            }
        } else {
            return null;
        }
    }


    /**
    * Check wether sitemap.xml exists for a given website
    *
    * @param object $website
    * @return string
    */

    public static function getSitemapXml(object $website)
    {
        // 'http_errors' set to false to disable throwing exceptions on an HTTP protocol errors
        $client = new Client(['http_errors' => false]);

        $url = $website->url;
        // Send request with SSL validation : 'verify' => false and with /robots.txt extension
        $response = $client->request('GET', $url . '/sitemap.xml', ['verify'  => true]);

        if ($response->getStatusCode() == 200) {
            $response = $response->getBody();

            $validator = Validator::make(['response' => $response], [
                'response' => 'required|starts_with:<?xml version'
            ]);
            // Return null if $response is not a sitemap file
            if ($validator->fails()) {
                return null;
            } else {
                return $response;
            }
        } else {
            return null;
        }
    }


    /**
    * Get WP extension datas from its URL
    *
    * @param object $website
    * @return string
    */

    public static function getWpExtensionDatas(object $website)
    {
        // 'http_errors' set to false to disable throwing exceptions on an HTTP protocol errors
        $client = new Client(['http_errors' => false]);
        // Associate URL and WP extension key
        $url = $website->url . env('WP_EXT_KEY');
        // Send request with SSL validation : 'verify' => false
        $response = $client->request('GET', $url, ['verify'  => true]);

        if ($response->getStatusCode() == 200) {
            $response = $response->getBody();

            $validator = Validator::make(['response' => $response], [
                'response' => 'required|json'
            ]);
            // Return null if $response is not a JSON format
            if ($validator->fails()) {
                return null;
            } else {
                return $response;
            }
        } else {
            return null;
        }
    }


    /**
    * Get WP extension datas from its URL
    *
    * @param object $website
    * @return string
    */

    public static function getHeader(object $website)
    {
        $response = get_headers($website->url, true);

        if ($response['0']) {
            $validator = Validator::make(['response' => $response], [
                'response' => 'required|array'
            ]);
            // Return null if $response is not an array
            if ($validator->fails()) {
                return null;
            } else {
                // When get_headers methods will return a status different from
                // 200, 301 or 302, an email mentionning associated url and such status
                // is sent to emilien@agencebcd.fr
                if ((preg_match('(200|301|302)', $response['0']) === 0)) {
                    $alert = [
                        "website" => $website->url,
                        "get_header_response" => $response
                    ];
                    Mail::to('emilien@agencebcd.fr')->send(new ErrorAlert($alert));
                }
                return json_encode($response);
            }
        } else {
            return null;
        }
    }
}

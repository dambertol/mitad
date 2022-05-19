<?php

(!defined('BASEPATH')) and exit('No direct script access allowed');

/**
 * CodeIgniter ORO CRM Class
 *
 * @package        	CodeIgniter
 * @subpackage    	Libraries
 * @category    	Libraries
 * @author        	Leandro
 * @created		24/09/2020
 */
class Oro
{

    private $apiUrl;
    private $username;
    private $userApiKey;

    public function __construct($apiUrl)
    {
        $this->apiUrl = $apiUrl;
    }

    /**
     * @param string $userName
     * @param string $userApiKey
     */
    public function login($userName, $userApiKey)
    {
        $this->userApiKey = $userApiKey;
        $this->username = $userName;
    }

    /**
     * @param $uri
     * @param $method
     * @param string|array $body
     *
     * @return mixed
     */
    public function execute($uri, $method, $body = null)
    {
        $ch = $this->initCurl($uri, $method, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if ($err)
        {
            throw new \RuntimeException($err);
        }

        if ($response and $res = json_decode($response, true))
        {
            return [$info['http_code'], $res];
        }

        return [$info['http_code'], []];
    }

    private function wsseHeader()
    {
        $nonce = base64_encode(substr(md5(uniqid('', true)), 0, 16));
        $created = date('c');
        $digest = base64_encode(sha1(base64_decode($nonce) . $created . $this->userApiKey, true));

        $wsseHeader[] = "Authorization: WSSE profile=\"UsernameToken\"";
        $wsseHeader[] = sprintf(
                'X-WSSE: UsernameToken Username="%s", PasswordDigest="%s", Nonce="%s", Created="%s"',
                $this->username,
                $digest,
                $nonce,
                $created
        );
        return $wsseHeader;
    }

    private function initCurl($uri, $method, $body = null)
    {
        $ch = \curl_init();
        \curl_setopt($ch, CURLOPT_URL, $this->apiUrl . $uri);
        \curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        \curl_setopt($ch, CURLOPT_TIMEOUT, 25);
        \curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        //\curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $headers = ["Content-Type: application/json"];
        if ($this->username && $this->userApiKey)
        {
            $headers = array_merge($this->wsseHeader(), $headers);
        }
        \curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if (is_array($body) && !empty($body))
        {
            $body = json_encode($body, TRUE);
        }
        if ($body)
        {
            \curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        return $ch;
    }
}

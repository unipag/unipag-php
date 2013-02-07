<?php

class Unipag_Api
{
    public static function request($method, $url,
                                   $params=array(), $api_key=null)
    {
        if (!$api_key) {
            $api_key = Unipag_Config::$api_key;
        }
        if (!$api_key) {
            throw new Unipag_Unauthorized(
'You did not provide an API key. There are 2 ways to do it:

1) set it globally via Unipag_Config, like this:

Unipag_Config::$api_key = "<your-key>";

2) pass it after array of arguments to methods which
communicate with the API, like this:

$invoice = Unipag_Invoice->create(array(...), "<your-key>")'
            );
        }
        if (!Unipag_Config::$ssl_verify
            && !Unipag_Utils::startsWith($api_key, 't_')) {
            trigger_error('Warning: using live Unipag API key with '.
                'SSL verification turned off.', E_USER_NOTICE);
        }

        /*** Prepare request ***/

        $curl_ver = curl_version();
        $client_info = array(
            'publisher' => 'Unipag',
            'platform' => php_uname(),
            'language' => 'PHP '.phpversion(),
            'http_lib' => 'curl '.$curl_ver['version'].
                            ', features: '.$curl_ver['features']
        );
        $headers = array(
            'Authorization: Basic '.base64_encode($api_key.':'),
            'User-Agent: Unipag Client for PHP, v'.Unipag_Client::VERSION,
            'X-Unipag-User-Agent-Info: '.json_encode($client_info)
        );

        $curl_options = array(
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CONNECTTIMEOUT => Unipag_Config::$connection_timeout,
            CURLOPT_TIMEOUT => Unipag_Config::$request_timeout,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => Unipag_Config::$ssl_verify,
        );
        if (!Unipag_Utils::startsWith($url, '/')) {
            $url = '/'.$url;
        }
        $abs_url = Unipag_Config::$api_url . $url;
        $params_encoded = Unipag_Utils::urlEncode($params);
        switch (strtolower($method)) {
            case 'get':
                $curl_options[CURLOPT_HTTPGET] = true;
                if ($params_encoded) {
                    $abs_url.= '?'.$params_encoded;
                }
                break;
            case 'post':
                $curl_options[CURLOPT_POST] = true;
                $curl_options[CURLOPT_POSTFIELDS] = $params_encoded;
                break;
            case 'delete':
                $curl_options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
                if ($params_encoded) {
                    $abs_url.= '?'.$params_encoded;
                }
                break;
            default:
                throw new Unipag_MethodNotAllowed(
                    "HTTP method not supported: $method"
                );
        }

        /*** Send request ***/

        $curl = curl_init($abs_url);
        curl_setopt_array($curl, $curl_options);
        $http_body = curl_exec($curl);

        /*** Handle connection errors ***/

        if ($http_body === false) {
            $err_code = curl_errno($curl);
            $err_msg = curl_error($curl);
            switch ($err_code) {
                case CURLE_COULDNT_CONNECT:
                case CURLE_COULDNT_RESOLVE_HOST:
                case CURLE_OPERATION_TIMEOUTED:
                    $msg = "Could not connect to Unipag at $abs_url. ".
                        "Please check your internet connection and try again.";
                    break;
                case CURLE_SSL_CACERT:
                case CURLE_SSL_PEER_CERTIFICATE:
                    $msg = 'Could not verify Unipag SSL certificate.
This could be a result of SSL inspection software running in your
network. You can check this by opening '.Unipag_Config::$api_url.' in
your browser. If that is true, in development environment you can turn
SSL verification off by setting Unipag_Config::ssl_verify = false. Note that
it should NOT be used in production, please always have ssl_verify = true
when dealing with real money.

If problem persists, please contact us at support@unipag.com.';
                    break;
                default:
                    $msg = 'Unexpected network error. If problem persists, '.
                        'please contact us at support@unipag.com';
            }

            $msg .= "\n\n(Network error [errno $err_code]: $err_msg)";
            throw new Unipag_ConnectionError($msg);
        }

        /*** Interpret Unipag response ***/

        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $json_body = json_decode($http_body, true);

        if (!$json_body) {
            throw new Unipag_ApiError(
                'Invalid response from the API (not a valid JSON). '.
                'If problem persists, please contact us at support@unipag.com',
                $http_code, $http_body, $json_body
            );
        }

        $err_msg = array_key_exists('error', $json_body)
                && array_key_exists('description', $json_body['error'])
                    ? $json_body['error']['description']
                    : 'Unknown error. If problem persists, '.
                        'please contact us at support@unipag.com';

        switch ($http_code) {
            case 200:
                return $json_body;
            case 400:
                throw new Unipag_BadRequest($err_msg, $http_code,
                                                    $http_body, $json_body);
            case 401:
                throw new Unipag_Unauthorized($err_msg, $http_code,
                                                    $http_body, $json_body);
            case 404:
                throw new Unipag_NotFound($err_msg, $http_code,
                                                    $http_body, $json_body);
            case 405:
                throw new Unipag_MethodNotAllowed($err_msg, $http_code,
                                                    $http_body, $json_body);
            case 500:
                throw new Unipag_InternalError($err_msg, $http_code,
                                                    $http_body, $json_body);
            case 503:
                throw new Unipag_ServiceUnavailable($err_msg, $http_code,
                                                    $http_body, $json_body);
            default:
                throw new Unipag_ApiError($err_msg, $http_code,
                                                    $http_body, $json_body);
        }
    }

    public static function get($url, $params=array(), $api_key=null)
    {
        return Unipag_Api::request('get', $url, $params, $api_key);
    }

    public static function post($url, $params=array(), $api_key=null)
    {
        return Unipag_Api::request('post', $url, $params, $api_key);
    }

    public static function delete($url, $params=array(), $api_key=null)
    {
        return Unipag_Api::request('delete', $url, $params, $api_key);
    }
}

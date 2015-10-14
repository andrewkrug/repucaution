<?php
/*
Copyright (c) 2010 Thai Pangsakulyanont

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

abstract class OAuthDamnitBase {

    var $consumer_key;
    var $consumer_secret;
    var $access_token = '';
    var $access_token_secret = '';

    abstract function http($method, $url, $data);

    function __construct($consumer_key, $consumer_secret, $access_token = '', $access_token_secret = '') {
        $this->consumer_key = $consumer_key;
        $this->consumer_secret = $consumer_secret;
        $this->access_token = $access_token;
        $this->access_token_secret = $access_token_secret;
    }
    function get($url, $params = array()) {
        return $this->request('GET', $url, $params);
    }
    function post($url, $params = array()) {
        return $this->request('POST', $url, $params);
    }
    function request($method, $url, $params = array()) {
        $signed_params = $this->sign($method, $url, $params);
        return $this->http($method, $url, $this->build_query($signed_params));
    }
    function sign($method, $url, $params = array()) {
        $params['oauth_consumer_key'] = $this->consumer_key;
        $params['oauth_nonce'] = $this->nonce();
        $params['oauth_signature_method'] = 'HMAC-SHA1';
        $params['oauth_timestamp'] = time();
        $params['oauth_version'] = '1.0';
        if (!empty($this->access_token)) {
            $params['oauth_token'] = $this->access_token;
        }
        $params['oauth_signature'] = $this->signature($method, $url, $params);
        return $params;
    }

    function signature($method, $url, $params = array()) {
        $query = $this->build_query($params);
        $base = $method . '&' . $this->encode($url) . '&' . $this->encode($query);
        $key = $this->consumer_secret . '&';
        if (!empty($this->access_token_secret)) {
            $key .= $this->access_token_secret;
        }
        return base64_encode($this->hmac_sha1($key, $base));
    }
    function build_query($params) {
        $output = array();
        foreach ($params as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $vv) {
                    $output[] = $this->encode($k) . ' ' . $this->encode($vv);
                }
            } else {
                $output[] = $this->encode($k) . ' ' . $this->encode($v);
            }
        }
        sort ($output, SORT_STRING);
        return str_replace(' ', '=', implode('&', $output));
    }
    function hmac_sha1($key, $message) {
        if (function_exists('hash_hmac')) {
            return hash_hmac('sha1', $message, $key, true);
        }
        if (strlen($key) > 64) {
            $key = sha1($key, true);
        }
        $key = str_pad($key, 64, "\0");
        $key1 = $key2 = '';
        for ($i = 0; $i < 64; $i ++) {
            $key1 .= chr(ord($key[$i]) ^ 0x5C);
            $key2 .= chr(ord($key[$i]) ^ 0x36);
        }
        return sha1($key1 . sha1($key2 . $message, true), true);
    }
    function nonce() {
        return bin2hex(md5(microtime(), true) . sha1(microtime(), true));
    }
    function encode($string) {
       // var_dump(str_replace('%7E', '~', rawurlencode($string)));
        return str_replace('%7E', '~', rawurlencode($string));
    }

}

class OAuthDamnit extends OAuthDamnitBase {

    var $ch;
    function curl_set_options() {
        curl_setopt ($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($this->ch, CURLOPT_USERAGENT, 'OAuth Damnit v0.1');
    }
    function curl_execute() {
        return curl_exec($this->ch);
    }
    function http($method, $url, $data) {
        if (!isset($this->ch)) {
            $this->ch = curl_init();
            $this->curl_set_options();
        }
        if ($method == 'POST') {
            curl_setopt ($this->ch, CURLOPT_URL, $url);
            curl_setopt ($this->ch, CURLOPT_POST, true);
            curl_setopt ($this->ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        } else {
            $append = strpos($url, '?') === false ? '?' : '&';
            curl_setopt ($this->ch, CURLOPT_URL, $url . $append . $data);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        }
        return $this->curl_execute();
    }

}
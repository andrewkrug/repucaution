<?php

/**
 * Get domain from the url with all subdomains
 *
 * @param $url (string)
 * @return string
 */
if (!function_exists('clear_domain')) {
    function clear_domain($url)
    {
        $url = preg_replace("/^((http|https):\/\/)*(www.)*/is", '', $url);
        $url = trim($url, '/');
        $pos = strpos($url, '/');
        if ($pos !== false) {
            $url = substr($url, 0, $pos);
        }
        $url = trim($url, '/');
        return $url;
    }
}

/**
 * Get relative path from absolute filesystem path
 *
 * @param $url (string)
 * @return string
 */
if (!function_exists('getRelativePath')) {
    function getRelativePath($path)
    {
        $relativePath = str_replace(
            strtolower(FCPATH),
            '',
            strtolower(str_replace('/', DIRECTORY_SEPARATOR, $path))
        );

        $relativePath = str_replace('\\', '/', $relativePath);

        return $relativePath;
    }
}

if (!function_exists('getDecodedUrlParts')) {
    /**
     * Return the url decoded right part of current url string
     * @param int $from - optional. Position to start
     * @param null $to - optional. Position to end
     * @param bool $preserveKeys
     * @return array
     */
    function getDecodedUrlParts($from = 0, $to = null, $preserveKeys = true)
    {

        $segments = get_instance()->uri->rsegments;
        if (!$to) {
            $to = count($segments);
        }
        if ($from) {
            $from -= 1;
        }
        $segments = array_slice($segments, $from, $to, $preserveKeys);
        foreach ($segments as $index => $value) {
            $segments[$index] = urldecode($value);
        }

        return $segments;
    }
}

if (!function_exists('site_url_from_system_path')) {
    /**
     * Convert system path to public url
     * @param string $path - system path
     * @return string  - public url
     * @throws Exception
     */
    function site_public_url_from_system_path($path) {
        if (!defined('PUBPATH')) {
            throw new \Exception('Public path is not defined.');
        } else {
            $replacePart = realpath(PUBPATH . '/..');
            $path = realpath($path);
            if ($path && stripos($path, $replacePart) === 0) {
                $url = str_replace($replacePart, rtrim(site_url(), '/'), $path);
                $url = str_replace('\\', '/', $url);
                return $url;
            }
        }
        throw new \Exception($path . ' is not a public directory');
    }
}
<?php

/*
 * This file is part of the SimplePageCrawler package.
 * @copyright Copyright (c) 2012 Blanchon Vincent - France (http://developpeur-zend-framework.fr - blanchon.vincent@gmail.com)
 */

namespace SimplePageCrawler;

use ArrayObject;
use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\Exception;

class LinkUtility extends AbstractOptions {

    protected static $_pattern = array(
        'hostname' => '(?:[_\p{L}0-9][-_\p{L}0-9]*\.)*(?:[\p{L}0-9][-\p{L}0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})'
    );

    protected static function _populateIp() {
        if (!isset(self::$_pattern['IPv6'])) {
            $pattern = '((([0-9A-Fa-f]{1,4}:){7}(([0-9A-Fa-f]{1,4})|:))|(([0-9A-Fa-f]{1,4}:){6}';
            $pattern .= '(:|((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})';
            $pattern .= '|(:[0-9A-Fa-f]{1,4})))|(([0-9A-Fa-f]{1,4}:){5}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})';
            $pattern .= '(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:)';
            $pattern .= '{4}(:[0-9A-Fa-f]{1,4}){0,1}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2}))';
            $pattern .= '{3})?)|((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:){3}(:[0-9A-Fa-f]{1,4}){0,2}';
            $pattern .= '((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|';
            $pattern .= '((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:){2}(:[0-9A-Fa-f]{1,4}){0,3}';
            $pattern .= '((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2}))';
            $pattern .= '{3})?)|((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:)(:[0-9A-Fa-f]{1,4})';
            $pattern .= '{0,4}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)';
            $pattern .= '|((:[0-9A-Fa-f]{1,4}){1,2})))|(:(:[0-9A-Fa-f]{1,4}){0,5}((:((25[0-5]|2[0-4]';
            $pattern .= '\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|((:[0-9A-Fa-f]{1,4})';
            $pattern .= '{1,2})))|(((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})))(%.+)?';

            self::$_pattern['IPv6'] = $pattern;
        }
        if (!isset(self::$_pattern['IPv4'])) {
            $pattern = '(?:(?:25[0-5]|2[0-4][0-9]|(?:(?:1[0-9])?|[1-9]?)[0-9])\.){3}(?:25[0-5]|2[0-4][0-9]|(?:(?:1[0-9])?|[1-9]?)[0-9])';
            self::$_pattern['IPv4'] = $pattern;
        }
    }

    static function rebuildUrlList($domain, $urls = array()) {

        return self::arrayUnique($domain, $urls);
        /*
          foreach ($urls as $url) {
          $url = $url['link']['href'];

          if ($url == '/') {
          continue;
          } elseif (!self::url($url)) {
          $url = str_replace('//', '/', $url);
          if (substr($url, 0, 1) == '/') {
          $url = $domain . $url;
          } else {
          $url = $domain . '/' . $url;
          }
          }

          //echo $url;
          //echo '<br>';
          //break;
          }
         * 
         */
    }

    static function arrayUnique($domain, $array, $preserveKeys = false) {
        // Unique Array for return  
        $arrayRewrite = array();
        // Array with the md5 hashes  
        $arrayHashes = array();
        foreach ($array as $key => $item) {
            // Serialize the current element and create a md5 hash  
            $hash = md5(serialize($item));
            // If the md5 didn't come up yet, add the element to  
            // to arrayRewrite, otherwise drop it  
            if (!isset($arrayHashes[$hash])) {
                // Save the current element hash  
                $arrayHashes[$hash] = $hash;
                // Add element to the unique Array  
                if ($preserveKeys) {
                    $arrayRewrite[$key] = $item;
                } else {

                    $url = $item['link']['href'];

                    if ($url == '/') {
                        continue;
                    } elseif (!self::url($url)) {
                        $url = str_replace('//', '/', $url);
                        if (substr($url, 0, 1) == '/') {
                            $url = $domain . $url;
                        } else {
                            $url = $domain . '/' . $url;
                        }
                    }

                    $do_host = parse_url($domain);
                    $url_host = parse_url($url);

                    if ($do_host['host'] == $url_host['host']) {
                        $item['link']['type'] = 'Internal';
                    } else {
                        $item['link']['type'] = 'External';
                    }

                    $item['link']['href'] = $url;

                    $arrayRewrite[] = $item;
                }
            }
        }
        return $arrayRewrite;
    }

    static function arrayUniqueFil($array, $preserveKeys = false) {
        // Unique Array for return  
        $arrayRewrite = array();
        // Array with the md5 hashes  
        $arrayHashes = array();
        foreach ($array as $key => $item) {
            // Serialize the current element and create a md5 hash  
            $hash = md5(serialize($item));
            // If the md5 didn't come up yet, add the element to  
            // to arrayRewrite, otherwise drop it  
            if (!isset($arrayHashes[$hash])) {
                // Save the current element hash  
                $arrayHashes[$hash] = $hash;
                // Add element to the unique Array  
                if ($preserveKeys) {
                    $arrayRewrite[$key] = $item;
                } else {
                    $arrayRewrite[] = $item;
                }
            }
        }
        return $arrayRewrite;
    }

    protected static function _check($check, $regex) {
        if (is_string($regex) && preg_match($regex, $check)) {
            return true;
        }
        return false;
    }

    public static function url($check, $strict = false) {
        self::_populateIp();
        $validChars = '([' . preg_quote('!"$&\'()*+,-.@_:;=~[]') . '\/0-9\p{L}\p{N}]|(%[0-9a-f]{2}))';
        $regex = '/^(?:(?:https?|ftps?|sftp|file|news|gopher):\/\/)' . (!empty($strict) ? '' : '?') .
                '(?:' . self::$_pattern['IPv4'] . '|\[' . self::$_pattern['IPv6'] . '\]|' . self::$_pattern['hostname'] . ')(?::[1-9][0-9]{0,4})?' .
                '(?:\/?|\/' . $validChars . '*)?' .
                '(?:\?' . $validChars . '*)?' .
                '(?:#' . $validChars . '*)?$/iu';
        return self::_check($check, $regex);
    }

}

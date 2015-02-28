<?php

/*
 * This file is part of the SimplePageCrawler package.
 * @copyright Copyright (c) 2012 Blanchon Vincent - France (http://developpeur-zend-framework.fr - blanchon.vincent@gmail.com)
 */

namespace SimplePageCrawler;

use Zend\Dom\Query as DomQuery;

class PageParser {

    public static function fromPageSource($source) {
        $response = new Response();
        $domQuery = new DomQuery();
        $domQuery->setDocumentHtml($source);

        $metas = array();
        $node = $domQuery->execute('meta[name="description"]');
        if ($node->offsetExists(0)) {
            $desc = $node->offsetGet(0)->getAttribute('content');
            $response->description = array('description' => $desc, 'length' => strlen($desc), 'impact' => 'bad');
        } else {
            $response->description = false;
        }
        $tags = $response->getHeadingTags();
        $h1 = array();
        $h2 = array();
        $h3 = array();
        $h4 = array();
        $h5 = array();
        $h6 = array();

        $nodes = $domQuery->queryXpath('//h1');
        foreach ($nodes as $node) {
            $h1[] = $node->textContent;
        }
        $nodes = $domQuery->queryXpath('//h2');
        foreach ($nodes as $node) {
            $h2[] = $node->textContent;
        }
        $nodes = $domQuery->queryXpath('//h3');
        foreach ($nodes as $node) {
            $h3[] = $node->textContent;
        }
        $nodes = $domQuery->queryXpath('//h4');
        foreach ($nodes as $node) {
            $h4[] = $node->textContent;
        }
        $nodes = $domQuery->queryXpath('//h5');
        foreach ($nodes as $node) {
            $h5[] = $node->textContent;
        }
        $nodes = $domQuery->queryXpath('//h6');
        foreach ($nodes as $node) {
            $h6[] = $node->textContent;
        }

        $tags->offsetSet('h1', $h1);
        $tags->offsetSet('h2', $h2);
        $tags->offsetSet('h3', $h3);
        $tags->offsetSet('h4', $h4);
        $tags->offsetSet('h5', $h5);
        $tags->offsetSet('h6', $h6);

        $node = $domQuery->queryXpath('//title')->current();
        if ($node) {
            $response->title = array('title' => $node->textContent, 'length' => strlen($node->textContent), 'impact' => 'bad');
        }

        $img = array();
        $nodes = $domQuery->queryXpath('//img');
        $hasAlt = 0;
        foreach ($nodes as $k => $node) {
            $img[$k]['image']['src'] = $node->getAttribute('src');
            $altLen = strlen(self::itrim($node->getAttribute('alt')));
            if ($altLen == 0) {
                $img[$k]['image']['alt'] = 0;
            } else {
                $img[$k]['image']['alt'] = self::itrim($node->getAttribute('alt'));
            }
            if ($node->hasAttribute('alt') && $altLen != 0) {
                $hasAlt = $hasAlt + 1;
            }
        }
        $img['hasAlt'] = $hasAlt;
        $response->getImages()->exchangeArray($img);


        $links = array();
        $nodes = $domQuery->queryXpath('//a');
        foreach ($nodes as $k => $node) {
            if (!$node->hasAttribute('href')) {
                continue;
            }
            $href = $node->getAttribute('href');
            $rel = $node->getAttribute('rel');
            if (
                    preg_match('/^#/', $href) ||
                    preg_match('/^javascript/', $href)
            ) {
                continue;
            }
            $links[$k]['link'] = array('href' => $href, 'rel' => $rel);
        }

        $response->links = $links;

        return $response;
    }

    static function arrayUnique($array, $preserveKeys = false) {
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

    static function multi_array_unique($links) {
        
    }

    static function itrim($string) {
        return preg_replace('/[\s^\n]+/', " ", trim($string));
    }

}

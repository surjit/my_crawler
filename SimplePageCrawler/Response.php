<?php

/*
 * This file is part of the SimplePageCrawler package.
 * @copyright Copyright (c) 2012 Blanchon Vincent - France (http://developpeur-zend-framework.fr - blanchon.vincent@gmail.com)
 */

namespace SimplePageCrawler;

use ArrayObject;
use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\Exception;

class Response extends AbstractOptions {
    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    public $description;

    /**
     * @var Response\Meta
     */
    protected $meta;

    /**
     * @var Response\Image
     */
    protected $images;

    /**
     * @var ArrayObject
     */
    protected $headingTags;

    /**
     * @var ArrayObject
     */
    protected $links;

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function getMeta($meta = null) {
        if (null === $this->meta) {
            $this->setMeta(new Response\Meta());
        }
        if ($meta) {
            return $this->meta->getMeta($meta);
        }
        return $this->meta;
    }

    public function setMeta(Response\Meta $meta) {
        $this->meta = $meta;
        return $this;
    }

    public function getImages() {
        if (null === $this->images) {
            $this->setImages(new Response\Image());
        }
        return $this->images;
    }

    public function setImages(Response\Image $images) {
        $this->images = $images;
        return $this;
    }

    public function getHeadingTags() {
        if (null === $this->headingTags) {
            $this->setHeadingTags(new ArrayObject());
        }
        return $this->headingTags;
    }

    public function setHeadingTags($headingTags) {
        if (is_array($headingTags)) {
            $this->getHeadingTags()->exchangeArray($headingTags);
            return $this;
        }
        if (!$headingTags instanceof ArrayObject) {
            throw new Exception\InvalidArgumentException('Heading tags must be an array or an ArrayObject');
        }
        $this->headingTags = $headingTags;
        return $this;
    }

    public function getH1() {
        return $this->headingTags['h1'];
    }

    public function getH2() {
        return $this->headingTags['h2'];
    }

    public function getH3() {
        return $this->headingTags['h3'];
    }

    public function getH4() {
        return $this->headingTags['h4'];
    }

    public function getH5() {
        return $this->headingTags['h5'];
    }

    public function getLinks() {
        if (null === $this->links) {
            $this->setLinks(new ArrayObject());
        }
        return $this->links;
    }

    public function setLinks($links) {
        if (is_array($links)) {
            $this->getLinks()->exchangeArray($links);
            return $this;
        }
        if (!$links instanceof ArrayObject) {
            throw new Exception\InvalidArgumentException('Links must be an array or an ArrayObject');
        }
        $this->links = $links;
        return $this;
    }

    public function get($property) {
        return $this->$property;
    }

    public function set($property, $value) {
        $this->$property = $value;
        return $this;
    }
    
    public function __call($val, $x) {
        if (substr($val, 0, 3) == 'get') {
            //$arr = $this->splitAtUpperCase(substr($val, 3));
            //$varname = strtolower(implode('_', $arr));
            
            $varname = strtolower(substr($val, 3));
            
            return $this->$varname;
            
        } else {
            throw new \Exception('Bad method.', 500);
        }
        if (property_exists('\SearchPageCrawler\Response', $varname)) {
            return $this->$varname;
        } else {
           throw new \Exception('Property does not exist: ' . $varname, 500);
        }
    }

    /**  As of PHP 5.3.0  */
    public static function __callStatic($name, $arguments) {
        // Note: value of $name is case sensitive.
        echo "Calling static method '$name' "
        . implode(', ', $arguments) . "\n";
    }

    function splitAtUpperCase($s) {
        return preg_split('/(?=[A-Z])/', $s, -1, PREG_SPLIT_NO_EMPTY);
    }

    public function itrim($string) {
        return preg_replace('/[\s^\n]+/', " ", trim($string));
    }

}

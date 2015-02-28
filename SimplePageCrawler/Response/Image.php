<?php

/*
 * This file is part of the SimplePageCrawler package.
 * @copyright Copyright (c) 2012 Blanchon Vincent - France (http://developpeur-zend-framework.fr - blanchon.vincent@gmail.com)
 */

namespace SimplePageCrawler\Response;

use ArrayObject;
use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\Exception;

class Image extends AbstractOptions {

    protected $icons;
    protected $images;

    public function exchangeArray($images) {
        $icons = array();
        $img = array();

        foreach ($images as $image) {
            if (preg_match('#\.ico$#', $image['image']['src'])) {
                $icons[] = $image;
            } else {
                $img[] = $image;
            }
        }
        $this->getIcons()->exchangeArray($icons);
        $this->getImages()->exchangeArray($img);
    }

    public function getArrayCopy() {
        return array_merge(
                $this->getIcons()->getArrayCopy(), $this->getImages()->getArrayCopy()
        );
    }

    public function getIcons() {
        if (null === $this->icons) {
            $this->setIcons(new ArrayObject());
        }
        return $this->icons;
    }

    public function setIcons($icons) {
        if (is_array($icons)) {
            $this->getImages()->exchangeArray($icons);
            return $this;
        }
        if (!$icons instanceof ArrayObject) {
            throw new Exception\InvalidArgumentException('Icones must be an array or an ArrayObject');
        }
        $this->icons = $icons;
        return $this;
    }

    public function getImages() {
        if (null === $this->images) {
            $this->setImages(new ArrayObject());
        }
        return $this->images;
    }

    public function setImages($images) {
        if (is_array($images)) {
            $this->getImages()->exchangeArray($images);
            return $this;
        }
        if (!$images instanceof ArrayObject) {
            throw new Exception\InvalidArgumentException('Images must be an array or an ArrayObject');
        }
        $this->images = $images;
        return $this;
    }

}

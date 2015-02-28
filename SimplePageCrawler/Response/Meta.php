<?php

/*
 * This file is part of the SimplePageCrawler package.
 * @copyright Copyright (c) 2012 Blanchon Vincent - France (http://developpeur-zend-framework.fr - blanchon.vincent@gmail.com)
 */

namespace SimplePageCrawler\Response;

use ArrayObject;
use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\Exception;

class Meta extends AbstractOptions
{
    /**
     * @var ArrayObject
     */
    protected $meta;

    /**
     * @var ArrayObject
     */
    protected $openGraph;

    public function exchangeArray($metas)
    {
        $meta = array();
        $openGraph = array();
        foreach($metas as $name => $value) {
            if(!preg_match('#^og:#', $name)) {
                $meta[$name] = $value;
            } else {
                $openGraph[preg_replace('#^og:#', '', $name)] = $value;
            }
        }
        $this->setMeta($meta);
        $this->getOpenGraph()->exchangeArray($openGraph);
    }

    public function getMeta($meta = null)
    {
        if(null === $this->meta) {
            $this->setMeta(new ArrayObject());
        }
        if($meta) {
            if(!$this->meta->offsetExists($meta)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Meta "%s" do not exists in meta tags', $meta
                ));
            }
            return $this->meta->offsetGet($meta);
        }
        return $this->meta;
    }

    public function setMeta($meta)
    {
        if(is_array($meta)) {
            $this->getMeta()->exchangeArray($meta);
            return $this;
        }
        if(!$meta instanceof ArrayObject) {
            throw new Exception\InvalidArgumentException('Meta must be an array or an ArrayObject');
        }
        $this->meta = $meta;
        return $this;
    }

    public function getOpenGraph()
    {
        if(null === $this->openGraph) {
            $this->setOpenGraph(new ArrayObject());
        }
        return $this->openGraph;
    }

    public function setOpenGraph($openGraph)
    {
        if(is_array($openGraph)) {
            $this->getLinks()->exchangeArray($openGraph);
            return $this;
        }
        if(!$openGraph instanceof ArrayObject) {
            throw new Exception\InvalidArgumentException('Open graph meta must be an array or an ArrayObject');
        }
        $this->openGraph = $openGraph;
        return $this;
    }
}

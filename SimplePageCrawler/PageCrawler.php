<?php

/*
 * This file is part of the SimplePageCrawler package.
 * @copyright Copyright (c) 2012 Blanchon Vincent - France (http://developpeur-zend-framework.fr - blanchon.vincent@gmail.com)
 */

namespace SimplePageCrawler;

use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Stdlib\Exception\InvalidArgumentException;

class PageCrawler {

    /**
     * The http client
     * @var Client
     */
    protected $httpClient;
    protected $response;
    protected $page_source;

    /**
     * Crawl & parse the uri
     * @param string $uri
     * @return Response
     */
    public function get($uri) {
        if ($uri instanceof Request) {
            $uri = $uri->getUri();
        }
        if (!is_string($uri)) {
            throw new InvalidArgumentException(
            'Uri must a string or instance of HttpRequest'
            );
        }

        $httpClient = $this->getHttpClient();
        $httpClient->setUri($uri);

        $this->response = $httpClient->send();
        $this->page_source = PageParser::fromPageSource($this->response);

        return $this;
    }

    /**
     * Get the http client
     * @return Client
     */
    public function getHttpClient() {
        if (null === $this->httpClient) {
            $this->setHttpClient(new Client());
        }
        return $this->httpClient;
    }

    /**
     * Set the http client
     * @param Client $httpClient
     * @return PageCrawler
     */
    public function setHttpClient(Client $httpClient) {
        $this->httpClient = $httpClient;
        return $this;
    }
    
    /**
     * Get the http client
     * @return Client
     */
    public function getResponse() {
        if (null === $this->response) {
            $this->setResponse($this->response);
        }
        return $this->response;
    }

    /**
     * Set the http client
     * @param Client $httpClient
     * @return PageCrawler
     */
    public function setResponse($response) {
        $this->response = $response;
        return $this;
    }
    
    /**
     * Get the http client
     * @return Client
     */
    public function getPageSource() {
        if (null === $this->page_source) {
            $this->setPageSource($this->page_source);
        }
        return $this->page_source;
    }

    /**
     * Set the http client
     * @param Client $httpClient
     * @return PageCrawler
     */
    public function setPageSource($page_source) {
        $this->page_source = $page_source;
        return $this;
    }
}

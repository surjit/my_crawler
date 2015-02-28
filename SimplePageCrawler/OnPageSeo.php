<?php

namespace SimplePageCrawler;

use ArrayObject;
use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\Exception;
use SimplePageCrawler as Crawler;

class OnPageSeo extends AbstractOptions {

    public $title;  //webpage
    public $description;  //webpage
    public $google_preview;  //webpage
    public $headings;  //webpage
    public $keywords;  //webpage
    public $keywords_cloud;  //webpage
    public $keywords_consistency;  //webpage
    public $alt_attribute;  //webpage
    public $text_html_ratio;  //webpage
    public $internal_pages_analysis;  //webpage
    public $popular_pages;  //webpage
    public $has_google_publisher = 0;  //webpage
    public $in_page_links;  //webpage
    public $broken_links;  //webpage
    public $www_resolve;  //webpage
    public $robots_txt;  //webpage
    public $xml_sitemap;  //webpage
    public $url_rewrite;  //webpage
    public $underscores_in_the_urls;  //webpage
    public $blocking_factors;  //webpage
    public $blog;  //webpage
    public $mobile_rendering; //webpage
    public $mobile_load_time; //webpage
    public $mobile_optimization; //webpage
    public $url; //webpage
    public $favicon; //webpage
    public $custom_404_page; //webpage
    public $conversion_forms; //webpage
    public $above_the_fold_content; //webpage
    public $page_size; //webpage
    public $load_time; //webpage
    public $language; //webpage
    public $printability; //webpage
    public $metadata; //webpage
    public $email_privacy; //webpage
    public $doctype;  //webpage
    public $indexed_pages;
    public $backlinks_counter;
    public $domain_registration;
    public $pagerank;
    public $ip_canonicalization;
    public $related_websites;
    public $domain_availability;
    public $typo_availability;
    public $spam_block;
    public $trust_indicators;
    public $safe_browsing;
    public $server_ip;
    public $technologies;
    public $speed_tips;
    public $analytics;
    public $w3c_validity;
    public $encoding;
    public $deprecated_html;
    public $directory_browsing;
    public $server_signature;
    public $social_shareability;
    public $facebook_page;
    public $twitter_account;
    public $google_page;
    public $local_directories;
    public $online_reviews;
    public $traffic_estimations;
    public $traffic_rank;
    public $adwords_traffic;
    public $visitors_localization;
    public $raw_headers;

    public function get($url) {

        $crawler = new Crawler\PageCrawler();
        $page_cralwer = $crawler->get($url);
        $kc = new Crawler\Keywords();


        $this->title = $page_cralwer->getPageSource()->getTitle();
        $this->description = $page_cralwer->getPageSource()->getDescription();
        $this->headings = $page_cralwer->getPageSource()->getHeadingTags();
        
        
        $this->in_page_links = array();// Crawler\LinkUtility::rebuildUrlList('http://yuldi.com', $page_cralwer->getPageSource()->getLinks());
        //$html = $page_cralwer->getResponse()->getBody();
        $this->keywords_cloud = array();//$kc->one_word_keywords($html); //array_slice($kc->keywordsArray($this->html), 0, 3);
        $this->keywords_consistency = array();//$kc->keywordDisplay($kc->one_word_keywords($html), $this->headings, $this->title, $this->description);


        $this->alt_attribute = $page_cralwer->getPageSource()->getImages()->getImages();
        $this->broken_links = array();
        $this->keywords = array(); //$kc->result($page_cralwer->getResponse()->getBody());

        return $this;
    }

    public function getBrokenLinks() {
        $crawler = new Crawler\PageCrawler();
        $page_cralwer = $crawler->get('http://qasta.co/yuldi.txt');
        $links = Crawler\LinkUtility::rebuildUrlList('http://yuldi.com', $page_cralwer->getPageSource()->getLinks());

        $linkStatus = array();

        foreach ($links as $key => $lin) {
            $link = $lin['link'];
            $client = new \Zend\Http\Client();
            $client->setAdapter('Zend\Http\Client\Adapter\BananaProtocol');
            $client->setOptions(array(
                'maxredirects' => 5,
                'timeout' => 30
            ));
            $response = $client->getResponse();
            $response->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8');


            $client->setUri($link['href']);
            $client->send();
            $linkStatus[] = array_merge($link, array('code' => $client->getResponse()->getStatusCode(), 'message' => $client->getResponse()->getReasonPhrase()));
        }
        print_r($linkStatus);
    }

    public function getWWWResolve($host) {
        $client = new \Zend\Http\Client();
        $client->setAdapter('Zend\Http\Client\Adapter\BananaProtocol');
        $client->setOptions(array(
            'maxredirects' => 5,
            'timeout' => 30
        ));
        $response = $client->getResponse();
        $response->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8');


        $client->setUri("http://{$host}")->send();
        $w = $client->getRequest()->getUri()->getHost();

        $client->setUri("http://www.{$host}")->send();
        $ww = $client->getRequest()->getUri()->getHost();

        if ($ww == $w) {
            return true;
        }

        return false;
    }

    public function hasRobot($host) {
        $client = new \Zend\Http\Client();
        $client->setAdapter('Zend\Http\Client\Adapter\BananaProtocol');
        $client->setOptions(array(
            'maxredirects' => 5,
            'timeout' => 30
        ));

        $client->setUri("http://{$host}/robots.txt")->send();
        $response = $client->getResponse();

        if ($response->getStatusCode() == 200 && $response->getReasonPhrase() == 'OK') {
            return true;
        }

        return false;
    }

}

<?php

namespace App\Helpers;

use App\Models\ShortLink;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Exception\RequestException;

class UrlParser
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Crawler
     */
    protected $crawler;

    /**
     * Create a new instance.
     *
     * @param Client $client
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get body of given url.
     *
     * @param string $url
     * @return string
     * @throws GuzzleException
     */
    public function getBody($url): string
    {
        try {
            $result = $this->client->request('GET', $url);
            $body = $result->getBody();
        } catch (RequestException $exception) {
            $body = '';
        }

        return $body;
    }

    /**
     * Parse the url to collect additional information.
     *
     * @param ShortLink $url
     * @return void
     * @throws GuzzleException
     */
    public function setUrlInfos(ShortLink $url): void
    {
        $crawler = new Crawler();
        $crawler->addHtmlContent($this->getBody($url->url));

        $titleNode = $crawler->filter('title');
        $descriptionNode = $crawler->filter('meta[name="description"]');

        // commented because it remove the title && the description of the ShortLink
        // $url->title = $titleNode->count() ? $titleNode->first()->text() : null;
        // $url->description = $descriptionNode->count() ? $descriptionNode->first()->attr('content') : null;
    }
}

<?php

declare(strict_types=1);

namespace Customerio;

use Customerio\Region\RegionInterface;
use GuzzleHttp\Client as BaseClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Utils;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;

class Client
{
    /** @var BaseClient $httpClient */
    private $httpClient;

    /** @var string API key */
    protected $apiKey;

    /** @var string site ID */
    protected $siteId;

    /** @var string App API key */
    protected $appKey;

    /** @var RegionInterface */
    protected $region;

    /** @var bool Assoc mode for response */
    protected $assocResponse;

    /** @var Endpoint\Events $events */
    public $events;

    /** @var Endpoint\Customers $customers */
    public $customers;

    /** @var Endpoint\Page $page */
    public $page;

    /** @var Endpoint\Campaigns */
    public $campaigns;

    /** @var Endpoint\Messages */
    public $messages;

    /** @var Endpoint\MessageTemplates */
    public $messageTemplates;

    /** @var Endpoint\Newsletters */
    public $newsletters;

    /** @var Endpoint\Segments */
    public $segments;

    /** @var Endpoint\Exports */
    public $exports;

    /** @var Endpoint\Activities */
    public $activities;

    /** @var Endpoint\SenderIdentities */
    public $senderIdentities;

    /** @var Endpoint\Send */
    public $send;

    /** @var Endpoint\Collections */
    public $collection;

    /**
     * Client constructor.
     * @param string $apiKey Api Key
     * @param string $siteId Site ID.
     * @param array $options client options
     */
    public function __construct(string $apiKey, string $siteId, array $options = [])
    {
        $this->setDefaultClient();

        switch ($options['version'] ?? 1) {
            case 1:
                $this->events = new Endpoint\Events($this);
                $this->customers = new Endpoint\Customers($this);
                $this->page = new Endpoint\Page($this);
                $this->campaigns = new Endpoint\Campaigns($this);
                $this->messages = new Endpoint\Messages($this);
                $this->messageTemplates = new Endpoint\MessageTemplates($this);
                $this->newsletters = new Endpoint\Newsletters($this);
                $this->segments = new Endpoint\Segments($this);
                $this->exports = new Endpoint\Exports($this);
                $this->activities = new Endpoint\Activities($this);
                $this->senderIdentities = new Endpoint\SenderIdentities($this);
                $this->send = new Endpoint\Send($this);
                $this->collection = new Endpoint\Collections($this);
                break;
            case 2:
                $this->customers = new Endpoint\V2\Customers($this);
                break;
            default:
                break;
        }

        $this->apiKey = $apiKey;
        $this->siteId = $siteId;
        $this->assocResponse = false;

        $this->region = Region::factory($options['region'] ?? 'us', $options['version'] ?? 1);
    }

    /**
     * @param string $appKey
     */
    public function setAppAPIKey(string $appKey): void
    {
        $this->appKey = $appKey;
    }

    /**
     * @param string $siteId
     */
    public function setSiteId(string $siteId): void
    {
        $this->siteId = $siteId;
    }

    /**
     * @param string $region
     */
    public function setRegion(string $region): void
    {
        $this->region = Region::factory($region);
    }

    /**
     * @return RegionInterface
     */
    public function getRegion(): RegionInterface
    {
        return $this->region;
    }

    /**
     * @param bool $assoc
     */
    public function setAssocResponse(bool $assoc): void
    {
        $this->assocResponse = $assoc;
    }

    /**
     * Set default client
     */
    private function setDefaultClient(): void
    {
        $this->httpClient = new BaseClient();
    }

    /**
     * Sets GuzzleHttp client.
     * @param BaseClient $client
     */
    public function setClient(BaseClient $client): void
    {
        $this->httpClient = $client;
    }

    /**
     * Get current Guzzle client
     * @return BaseClient
     */
    public function getClient(): BaseClient
    {
        return $this->httpClient;
    }

    /**
     * Sends GET request to Customer.io API.
     * @param string $endpoint
     * @param array $params
     * @return mixed
     * @throws GuzzleException
     */
    public function get(string $endpoint, array $params = [])
    {
        $apiEndpoint = $this->getRegion()->apiUri();

        $options = $this->getDefaultParams($apiEndpoint);
        if (!empty($params)) {
            $options['query'] = $params;
        }

        $response = $this->httpClient->request('GET', $apiEndpoint.$endpoint, $options);

        if (isset($params['raw'])) {
            return (string)$response->getBody();
        }

        return $this->handleResponse($response);
    }

    /**
     * Sends POST request to Customer.io API.
     * @param string $endpoint
     * @param array $json
     * @return mixed
     * @throws GuzzleException
     */
    public function post(string $endpoint, array $json)
    {
        $response = $this->request('POST', $endpoint, $json);

        return $this->handleResponse($response);
    }

    /**
     * Sends DELETE request to Customer.io API.
     * @param string $endpoint
     * @param array $json
     * @return mixed
     * @throws GuzzleException
     */
    public function delete(string $endpoint, array $json)
    {
        $response = $this->request('DELETE', $endpoint, $json);

        return $this->handleResponse($response);
    }

    /**
     * Sends PUT request to Customer.io API.
     * @param string $endpoint
     * @param array $json
     * @return mixed
     * @throws GuzzleException
     */
    public function put(string $endpoint, array $json)
    {
        $response = $this->request('PUT', $endpoint, $json);

        return $this->handleResponse($response);
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $json
     * @return ResponseInterface
     * @throws GuzzleException
     */
    protected function request(string $method, string $path, array $json): ResponseInterface
    {
        $apiEndpoint = $this->region->trackUri();

        if (isset($json['endpoint'])) {
            $apiEndpoint = $json['endpoint'];
            unset($json['endpoint']);
        }

        $options = $this->getDefaultParams($apiEndpoint);
        $url = $apiEndpoint.$path;

        if (!empty($json)) {
            if (!empty($json['query'])) {
                $options['query'] = $json['query'];
                unset($json['query']);
            }

            $options['json'] = $json;
        }

        return $this->httpClient->request($method, $url, $options);
    }

    /**
     * Returns authentication parameters.
     * @return array
     */
    public function getAuth(): array
    {
        return [$this->siteId, $this->apiKey];
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        if (empty($this->appKey)) {
            throw new InvalidArgumentException("App API Key not set!");
        }

        return $this->appKey;
    }

    /**
     * @param ResponseInterface $response
     * @return mixed
     */
    private function handleResponse(ResponseInterface $response)
    {
        $stream = Utils::streamFor($response->getBody());

        return json_decode($stream->getContents(), $this->assocResponse);
    }

    /**
     * Get default Guzzle options
     * @param $endpoint
     * @return array
     */
    protected function getDefaultParams($endpoint): array
    {
        switch ($endpoint) {
            case $this->region->apiUri():
                return [
                    'headers' => [
                        'Authorization' => 'Bearer '.$this->getToken(),
                        'Accept' => 'application/json',
                    ],
                    'connect_timeout' => 2,
                    'timeout' => 5,
                ];
            default:
                return [
                    'auth' => $this->getAuth(),
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                    'connect_timeout' => 2,
                    'timeout' => 5,
                ];
        }
    }

    public function events(): Endpoint\Events
    {
        return $this->events;
    }

    public function customers(): Endpoint\Customers
    {
        return $this->customers;
    }

    public function page(): Endpoint\Page
    {
        return $this->page;
    }

    public function campaigns(): Endpoint\Campaigns
    {
        return $this->campaigns;
    }

    public function messages(): Endpoint\Messages
    {
        return $this->messages;
    }

    public function messageTemplates(): Endpoint\MessageTemplates
    {
        return $this->messageTemplates;
    }

    public function newsletters(): Endpoint\Newsletters
    {
        return $this->newsletters;
    }

    public function segments(): Endpoint\Segments
    {
        return $this->segments;
    }

    public function exports(): Endpoint\Exports
    {
        return $this->exports;
    }

    public function activities(): Endpoint\Activities
    {
        return $this->activities;
    }

    public function senderIdentities(): Endpoint\SenderIdentities
    {
        return $this->senderIdentities;
    }

    public function send(): Endpoint\Send
    {
        return $this->send;
    }

    public function collection(): Endpoint\Collections
    {
        return $this->collection;
    }
}

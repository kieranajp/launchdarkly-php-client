<?php declare(strict_types=1);

namespace HelloFresh\LaunchDarkly;

use Zend\Diactoros\Uri;
use Zend\Diactoros\Request;
use Http\Discovery\HttpClientDiscovery;
use Http\Client\Common\HttpMethodsClient;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\HttpAsyncClientDiscovery;

final class Client
{
    /** @var string */
    private $apikey;

    /** @var string */
    private $environment;

    /** @var HttpMethodsClient */
    private $httpClient;

    /** LaunchDarkly SDK path */
    const BASE_URI = 'https://app.launchdarkly.com/sdk';

    /**
     * @param string $apikey
     */
    public function __construct(string $apikey, string $environment = 'production')
    {
        $this->apikey = $apikey;
        $this->environment = $environment;
        $this->httpClient = HttpClientDiscovery::find();
    }

    /**
     * Check if a feature is enabled.
     *
     * @param  string  $feature
     * @return boolean
     */
    public function isEnabled(string $feature) : bool
    {
        $request = (new Request())
            ->withUri(new Uri(sprintf('%s/flags/%s', self::BASE_URI, $feature)))
            ->withMethod('GET')
            ->withAddedHeader('User-Agent', 'HelloFresh/LaunchDarkly-PHP-Client')
            ->withAddedHeader('Content-Type', 'application/json')
            ->withAddedHeader('Authorization', sprintf('api_key %s', $this->apikey));

        return json_decode((string) $this->httpClient->sendRequest($request)->getBody())->on;
    }
}

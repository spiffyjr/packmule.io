<?php

namespace Destiny\Client;

use Closure;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Event\CompleteEvent;
use GuzzleHttp\Event\ErrorEvent;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Pool;

abstract class AbstractClient
{
    /** @var GuzzleHttpClient */
    private $guzzle;
    /** @var \GuzzleHttp\Message\Request[] */
    private $requestsToSend = [];

    /**
     * @param GuzzleHttpClient $guzzle
     */
    public function __construct(GuzzleHttpClient $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    /**
     * {@inheritDoc}
     */
    public function queue($method, $endpoint, array $params = [], Closure $callback = null, array $options = [])
    {
        $request = $this->guzzle->createRequest($method, $this->createUriFromEndpoint($endpoint, $params), $options);

        $request->getConfig()->set('params', $params);
        $request->getConfig()->set('callback', $callback);

        $this->requestsToSend[] = $request;
    }

    /**
     * {@inheritDoc}
     */
    public function queueRequest(RequestInterface $request, Closure $callback = null)
    {
        $request->getConfig()->set('callback', $callback);
        $this->requestsToSend[] = $request;
    }

    /**
     * {@inheritDoc}
     */
    public function send()
    {
        $pool = new Pool(
            $this->guzzle,
            $this->requestsToSend,
            [
                'complete' => function (CompleteEvent $event) {
                    $request = $event->getRequest();
                    $callback = $request->getConfig()->get('callback');

                    if (is_callable($callback)) {
                        $callback($event);
                    }
                },
                'error' => function (ErrorEvent $event) {
                    echo $event->getResponse()->getStatusCode();
                }
            ]
        );
        $pool->wait();
    }

    /**
     * @return GuzzleHttpClient
     */
    public function getGuzzle()
    {
        return $this->guzzle;
    }

    /**
     * @param string $endpoint
     * @param array $params
     * @return string
     */
    public function createUriFromEndpoint($endpoint, array $params = [])
    {
        foreach ($params as $key => $value) {
            $endpoint = str_replace('{' . $key . '}', $value, $endpoint);
        }
        return $endpoint;
    }
}

<?php
/**
 * @category  Mage4
 * @package   Mage4_Turnstile
 * @author    Rehan Mobin <m.rehan.mobin@gmail.com>
 * @copyright Copyright (c) Mage4. All rights reserved. (https://www.mage4.com)
 */

namespace Mage4\Turnstile\Model\Turnstile;

use GuzzleHttp\Exception\RequestException;
use Prewk\Result;
use Mage4\Turnstile\Model\Config;
use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\ResponseInterface;
use Prewk\Result\{Ok, Err};

final class TurnstileApiClient implements TurnstileApi
{
    const BASE_URI = 'https://challenges.cloudflare.com/turnstile/v0/';

    private Config $config;
    private GuzzleClient $client;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->client = new GuzzleClient(['base_uri' => self::BASE_URI, 'timeout' => 3, 'headers' => ['Content-Type' => 'application/json']]);
    }

    public function validateWidgetResponse(string $widgetResponse): Result
    {
        return $this->request('POST', 'siteverify', [
            'json' => [
                'secret' => $this->config->getSecretKey(),
                'response' => $widgetResponse
            ]
        ])->map(function (array $data): SiteverifyResponse {
            return SiteverifyResponse::createFromResponse($data);
        })->andThen(function (SiteverifyResponse $response) {
            if ($response->isFailure()) {
                return new Err($response);
            }
            return new Ok($response);
        });
    }

    private function request(string $method, string $path, array $options = []): Result
    {
        $res = $this->client->requestAsync($method, $path, $options)->then(function(ResponseInterface $resp) {
            return new Result\Ok(json_decode((string) $resp->getBody(), true));
        }, function(\Throwable $e) {
            if (!$e instanceof RequestException) {
                return new Result\Err([null, null, $e]);
            }
            $httpResponse = $e->getResponse();
            if ($httpResponse === null) {
                return new Result\Err([null, null, $e]);
            }

            $resp = json_decode((string) $httpResponse->getBody(), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($resp)) {
                return new Result\Err([$resp, $httpResponse, $e]);
            }

            return new Result\Err([null, $httpResponse, $e]);
        });
        return $res->wait();
    }
}

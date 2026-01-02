<?php
namespace Framework\Turnstile;
use Framework\Contracts\TurnstileContract;
use GuzzleHttp\Client;

class TurnstileGuzzle implements TurnstileContract
{
    public function __construct(private Client $client) {}

    public function post(string $url, array $form): array
    {
        $res = $this->client->post($url, ['form_params' => $form, 'timeout' => 5]);
        return ['status' => $res->getStatusCode(), 'body' => (string)$res->getBody()];
    }
}

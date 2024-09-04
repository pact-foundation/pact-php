<?php

namespace FormUrlEncodedConsumer\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;

class HttpClientService
{
    private Client $httpClient;

    private string $baseUri;

    public function __construct(string $baseUri)
    {
        $this->httpClient = new Client();
        $this->baseUri    = $baseUri;
    }

    public function createUser(): string
    {
        $response = $this->httpClient->post(new Uri("{$this->baseUri}/users"), [
            'body' => http_build_query([
                'empty' => '',
                'agree' => 'true',
                'fullname' => 'First Last Name',
                'email' => 'user@example.test',
                'password' => 'very@secure&password123',
                'age' => 41,
                'ampersand' => '&',
                'slash' => '/',
                'question-mark' => '?',
                'equals-sign' => '=',
                '&' => 'ampersand',
                '/' => 'slash',
                '?' => 'question-mark',
                '=' => 'equals-sign',
            ]) .
            '&=first&=second&=third' .
            '&roles[]=User&roles[]=Manager' .
            '&orders[]=&orders[]=ASC&orders[]=DESC',
            'headers' => [
                'Accept' => 'application/x-www-form-urlencoded',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ]);

        return $response->getBody();
    }
}

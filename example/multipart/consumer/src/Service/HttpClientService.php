<?php

namespace MultipartConsumer\Service;

use GuzzleHttp\Client;

/**
 * Example HTTP Service
 */
class HttpClientService
{
    private Client $httpClient;

    private string $baseUri;

    public function __construct(string $baseUri)
    {
        $this->httpClient = new Client();
        $this->baseUri = $baseUri;
    }

    public function updateUserProfile(): string
    {
        $response = $this->httpClient->post("{$this->baseUri}/user-profile", [
            'multipart' => [
                [
                    'name' => 'full_name',
                    'contents' => 'Zoey Turcotte',
                    'filename' => 'full_name.txt',
                    'headers' => [
                        'Content-Type' => 'application/octet-stream',
                    ],
                ],
                [
                    'name' => 'profile_image',
                    'contents' => file_get_contents(__DIR__ . '/../_resource/image.jpg'),
                    'filename' => 'image.jpg',
                ],
                [
                    'name' => 'personal_note',
                    'contents' => 'testing',
                    'filename' => 'note.txt',
                    'headers' => [
                        'X-Foo' => 'this is a note',
                        'Content-Type' => 'application/octet-stream',
                    ],
                ],
            ],
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ZmluLWFwaTphcGktc2VjcmV0',
            ],
        ]);

        return $response->getBody();
    }
}

<?php

namespace MultipartConsumer\Service;

use GuzzleHttp\Client;

/**
 * Example HTTP Service
 */
class HttpClientService
{
    private Client $httpClient;

    public function __construct(private string $baseUri)
    {
        $this->httpClient = new Client();
    }

    public function updateUserProfile(): string
    {
        $response = $this->httpClient->post("{$this->baseUri}/user-profile", [
            'multipart' => [
                [
                    'name' => 'full_name',
                    'contents' => 'Zoey Turcotte',
                    'filename' => 'full_name.txt',
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

<?php

namespace MultipartConsumer\Tests\Service;

use MultipartConsumer\Service\HttpClientService;
use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Consumer\Matcher\Matcher;
use PhpPact\Consumer\Model\Body\Multipart;
use PhpPact\Consumer\Model\Body\Part;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Standalone\MockService\MockServerConfig;
use PHPUnit\Framework\TestCase;

class HttpClientServiceTest extends TestCase
{
    public const URL_FORMAT = '^https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()!@:%_\+.~#?&\/\/=]*)';

    public function testUpdateUserProfile()
    {
        $matcher = new Matcher();
        $fullName = 'Colten Ziemann';
        $profileImageUrl = 'http://example.test/profile-image.jpg';
        $personalNote = 'testing';

        $request = new ConsumerRequest();
        $request
            ->setMethod('POST')
            ->setPath('/user-profile')
            ->setHeaders([
                'Accept' => 'application/json',
                'Authorization' => [
                    \json_encode($matcher->like('Bearer eyJhbGciOiJIUzI1NiIXVCJ9'))
                ],
            ])
            ->setBody(new Multipart(
                [
                    new Part(__DIR__ . '/../_resource/full_name.txt', 'full_name', 'text/plain'),
                    new Part(__DIR__ . '/../_resource/image.jpg', 'profile_image', 'image/jpeg'),
                    new Part(__DIR__ . '/../_resource/note.txt', 'personal_note', 'text/plain'),
                ],
                'ktJmeYHbkTSa1jxD'
            ));

        $response = new ProviderResponse();
        $response
            ->setStatus(200)
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'full_name' => $matcher->like($fullName),
                'profile_image' => $matcher->regex($profileImageUrl, self::URL_FORMAT),
                'personal_note' => $matcher->like($personalNote),
            ]);

        $config = new MockServerConfig();
        $config
            ->setConsumer('multipartConsumer')
            ->setProvider('multipartProvider')
            ->setPactDir(__DIR__.'/../../../pacts');
        if ($logLevel = \getenv('PACT_LOGLEVEL')) {
            $config->setLogLevel($logLevel);
        }
        $builder = new InteractionBuilder($config);
        $builder
            ->given('User exists')
            ->uponReceiving('A put request to /user-profile')
            ->with($request)
            ->willRespondWith($response);

        $service = new HttpClientService($config->getBaseUri());
        $userProfileResponse = $service->updateUserProfile();
        $verifyResult = $builder->verify();

        $this->assertTrue($verifyResult);
        $this->assertEquals([
            'full_name' => $fullName,
            'profile_image' => $profileImageUrl,
            'personal_note' => $personalNote,
        ], \json_decode($userProfileResponse, true, 512, JSON_THROW_ON_ERROR));
    }
}

<?php

use PHPUnit\Framework\TestCase;

class PactBuilderTest extends TestCase
{
    const TEMP_PACT_DIR = "../temp-test-pact";

    public static function setUpBeforeClass()
    {
        if (!is_dir(self::TEMP_PACT_DIR)) {
            mkdir(self::TEMP_PACT_DIR);
        }
    }

    public static function tearDownAfterClass()
    {
        if (is_dir(self::TEMP_PACT_DIR)) {
            array_map('unlink', glob(self::TEMP_PACT_DIR . "/*.*"));
            rmdir(self::TEMP_PACT_DIR);
        }
    }

    public function testBuildV2()
    {
        // build pact file
        $pactFile = new \PhpPact\Mocks\MockHttpService\Models\ProviderServicePactFile();
        $pactFile->setProvider(new \PhpPact\Models\Pacticipant("testBuildProviderV2"));
        $pactFile->setConsumer(new \PhpPact\Models\Pacticipant("testBuildConsumerV2"));

        $json = '{"description":"A GET request","providerState":"Some types","request":{"method":"get","path":"/Call/","headers":{"Content-Type":"application/json"}},"response":{"status":200,"headers":{"Content-Type":"application/json"},"body":{"types":[{"id":1000}]}}}';
        $interaction1 = \json_decode($json);

        $json = '{"description":"Another GET request","providerState":"Some more types","request":{"method":"get","path":"/Call/","headers":{"Content-Type":"application/json"}},"response":{"status":200,"headers":{"Content-Type":"application/json"},"body":{"types":[{"id":1000}]}}}';
        $interaction2 = \json_decode($json);

        $json = '{"description":"a request to retrieve event with id \'83f9262f-28f1-4703-ab1a-8cfd9e8249c9\'","providerState":"there is an event with id \'83f9262f-28f1-4703-ab1a-8cfd9e8249c9\'","request":{"method":"get","path":"/events/83f9262f-28f1-4703-ab1a-8cfd9e8249c9","headers":{"Accept":"application/json"},"matchingRules":{"$.path":{"match":"regex","regex":"^\\/events\\/[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$"}}},"response":{"status":200,"headers":{"Content-Type":"application/json; charset=utf-8","Server":"RubyServer"},"body":{"eventId":"83f9262f-28f1-4703-ab1a-8cfd9e8249c9","eventType":"DetailsView","timestamp":"2017-08-28T11:59:29.7068433Z"},"matchingRules":{"$.headers.Server":{"match":"type"},"$.body.eventId":{"match":"regex","regex":"^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$"},"$.body.eventType":{"match":"type"},"$.body.timestamp":{"match":"regex","regex":"^(-?(?:[1-9][0-9]*)?[0-9]{4})-(1[0-2]|0[1-9])-(3[0-1]|0[1-9]|[1-2][0-9])T(2[0-3]|[0-1][0-9]):([0-5][0-9]):([0-5][0-9])(\\\\.[0-9]+)?(Z|[+-](?:2[0-3]|[0-1][0-9]):[0-5][0-9])?$"}}}}';
        $interaction3 = \json_decode($json);

        $expectedInteractions = array();
        $expectedInteractions[] = $interaction1;
        $expectedInteractions[] = $interaction2;
        $expectedInteractions[] = $interaction3;

        $pactFile->setInteractions($expectedInteractions);
        // should be v2 by default
        //$pactFile->setPactSpecificationVersion(\PhpPact\Mocks\MockHttpService\Models\ProviderServicePactFile::SPECIFICATION_VERSION_2);

        $this->runBuild($pactFile);
    }


    /**
     * Test that a specification for v1.1 can be built
     *
     * @test
     */
    public function testBuildV1dot1()
    {
        // build pact file
        $pactFile = new \PhpPact\Mocks\MockHttpService\Models\ProviderServicePactFile();
        $pactFile->setProvider(new \PhpPact\Models\Pacticipant("testBuildProviderV1dot1"));
        $pactFile->setConsumer(new \PhpPact\Models\Pacticipant("testBuildConsumerV1dot1"));

        $json = '{"description":"A GET request","providerState":"Some types","request":{"method":"get","path":"/Call/","headers":{"Content-Type":"application/json"}},"response":{"status":200,"headers":{"Content-Type":"application/json"},"body":{"types":[{"id":1000}]}}}';
        $interaction1 = \json_decode($json);

        $json = '{"description":"Another GET request","providerState":"Some more types","request":{"method":"get","path":"/Call/","headers":{"Content-Type":"application/json"}},"response":{"status":200,"headers":{"Content-Type":"application/json"},"body":{"types":[{"id":1000}]}}}';
        $interaction2 = \json_decode($json);

        $expectedInteractions = array();
        $expectedInteractions[] = $interaction1;
        $expectedInteractions[] = $interaction2;

        $pactFile->setInteractions($expectedInteractions);
        $pactFile->setPactSpecificationVersion(\PhpPact\Mocks\MockHttpService\Models\ProviderServicePactFile::SPECIFICATION_VERSION_1);

        $this->runBuild($pactFile);
    }


    private function runBuild(\PhpPact\Mocks\MockHttpService\Models\ProviderServicePactFile $pactFile) {
        // build config with new location
        $config = new \PhpPact\PactConfig();
        $config->setPactDir(self::TEMP_PACT_DIR);

        // build and run ->Build()
        $build = (new \PhpPact\PactBuilder())
            ->serviceConsumer($pactFile->getConsumer()->getName())
            ->hasPactWith($pactFile->getProvider()->getName());
        $build->setConfig($config);
        $build->setMockService($pactFile->getProvider()->getName(), $config);

        // verify the interactions
        $hasException = false;
        try {
            $build->build($pactFile);
            $expectedFile = self::TEMP_PACT_DIR . '/' . $pactFile->getFileName();
            $this->assertTrue(file_exists($expectedFile), sprintf("We expect pact file to be written: %s", $pactFile->getFileName()));
        } catch (\PhpPact\PactFailureException $e) {
            $hasException = true;
        }
        $this->assertFalse($hasException, "This basic get should verify the interactions and not throw an exception");

    }

}

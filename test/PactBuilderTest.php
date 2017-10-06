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

    public function testBuild()
    {

        // build pact file
        $pactFile = new \PhpPact\Mocks\MockHttpService\Models\ProviderServicePactFile();
        $pactFile->setProvider(new \PhpPact\Models\Pacticipant("testBuildProvider"));
        $pactFile->setConsumer(new \PhpPact\Models\Pacticipant("testBuildConsumer"));

        $json = '{"description":"A GET request","provider_state":"Some types","request":{"method":"get","path":"/Call/","headers":{"Content-Type":"application/json"}},"response":{"status":200,"headers":{"Content-Type":"application/json"},"body":{"types":[{"id":1000}]}}}';
        $interaction1 = \json_decode($json);

        $json = '{"description":"Another GET request","provider_state":"Some more types","request":{"method":"get","path":"/Call/","headers":{"Content-Type":"application/json"}},"response":{"status":200,"headers":{"Content-Type":"application/json"},"body":{"types":[{"id":1000}]}}}';
        $interaction2 = \json_decode($json);

        $expectedInteractions = array();
        $expectedInteractions[] = $interaction1;
        $expectedInteractions[] = $interaction2;

        $pactFile->setInteractions($expectedInteractions);

        // build config with new location
        $config = new \PhpPact\PactConfig();
        $config->setPactDir(self::TEMP_PACT_DIR);

        // build and run ->Build()
        $build = (new \PhpPact\PactBuilder())
            ->ServiceConsumer($pactFile->getConsumer()->getName())
            ->HasPactWith($pactFile->getProvider()->getName());
        $build->setConfig($config);
        $build->setMockService($pactFile->getProvider()->getName(), $config);

        // verify the interactions
        $hasException = false;
        try {
            $build->Build($pactFile);
            $expectedFile = self::TEMP_PACT_DIR . '/' . $pactFile->getFileName();
            $this->assertTrue(file_exists($expectedFile), sprintf("We expect pact file to be written: %s", $pactFile->getFileName()));

        } catch (\PhpPact\PactFailureException $e) {
            $hasException = true;
        }
        $this->assertFalse($hasException, "This basic get should verify the interactions and not throw an exception");
        // assert no exception


    }

}

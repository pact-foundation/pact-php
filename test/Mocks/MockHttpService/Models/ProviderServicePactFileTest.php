<?php

namespace Mocks\MockHttpService\Models;

use PhpPact\Mocks\MockHttpService\Models\ProviderServicePactFile;
use PHPUnit\Framework\TestCase;

class ProviderServicePactFileTest extends TestCase
{
    public function testFilterInteractionsByDescription()
    {
        $json = '{"description":"A GET request","providerState":"Some types","request":{"method":"get","path":"/Call/","headers":{"Content-Type":"application/json"}},"response":{"status":200,"headers":{"Content-Type":"application/json"},"body":{"types":[{"id":1000}]}}}';
        $interaction1 = \json_decode($json);

        $json = '{"description":"Another GET request","providerState":"Some more types","request":{"method":"get","path":"/Call/","headers":{"Content-Type":"application/json"}},"response":{"status":200,"headers":{"Content-Type":"application/json"},"body":{"types":[{"id":1000}]}}}';
        $interaction2 = \json_decode($json);

        $json = '{"description":"A GET request with a different state","provider_state":"Now with more underscores","request":{"method":"get","path":"/Call/","headers":{"Content-Type":"application/json"}},"response":{"status":200,"headers":{"Content-Type":"application/json"},"body":{"types":[{"id":1000}]}}}';
        $interaction3 = \json_decode($json);

        $expectedInteractions = array();
        $expectedInteractions[] = $interaction1;
        $expectedInteractions[] = $interaction2;
        $expectedInteractions[] = $interaction3;

        $pactFile = new ProviderServicePactFile();
        $pactFile->setInteractions($expectedInteractions);
        $pactFile->filterInteractionsByDescription("Another GET request");
        $actualInteractions = $pactFile->getInteractions();
        $this->assertEquals(1, count($actualInteractions), "Check that one interactions is left");

        $actualInteraction1 = $actualInteractions[0];
        $this->assertEquals("Another GET request", $actualInteraction1->getDescription(), "Check that an interaction has a description");


        // empty case
        $json = '{"description":"A GET request","providerState":"Some types","request":{"method":"get","path":"/Call/","headers":{"Content-Type":"application/json"}},"response":{"status":200,"headers":{"Content-Type":"application/json"},"body":{"types":[{"id":1000}]}}}';
        $interaction1 = \json_decode($json);

        $json = '{"description":"Another GET request","providerState":"Some more types","request":{"method":"get","path":"/Call/","headers":{"Content-Type":"application/json"}},"response":{"status":200,"headers":{"Content-Type":"application/json"},"body":{"types":[{"id":1000}]}}}';
        $interaction2 = \json_decode($json);

        $expectedInteractions = array();
        $expectedInteractions[] = $interaction1;
        $expectedInteractions[] = $interaction2;

        $pactFile = new ProviderServicePactFile();
        $pactFile->setInteractions($expectedInteractions);
        $pactFile->filterInteractionsByDescription("None existent description");
        $actualInteractions = $pactFile->getInteractions();

        $this->assertEquals(0, count($actualInteractions), "No interactions returned");
    }

    public function testFilterInteractionsByProviderState()
    {
        $json = '{"description":"A GET request","providerState":"Some types","request":{"method":"get","path":"/Call/","headers":{"Content-Type":"application/json"}},"response":{"status":200,"headers":{"Content-Type":"application/json"},"body":{"types":[{"id":1000}]}}}';
        $interaction1 = \json_decode($json);

        $json = '{"description":"Another GET request","providerState":"Some more types","request":{"method":"get","path":"/Call/","headers":{"Content-Type":"application/json"}},"response":{"status":200,"headers":{"Content-Type":"application/json"},"body":{"types":[{"id":1000}]}}}';
        $interaction2 = \json_decode($json);

        $expectedInteractions = array();
        $expectedInteractions[] = $interaction1;
        $expectedInteractions[] = $interaction2;

        $pactFile = new ProviderServicePactFile();
        $pactFile->setInteractions($expectedInteractions);

        $actualInteractions = $pactFile->filterInteractionsByProviderState("Some types");
        $this->assertEquals(1, count($actualInteractions), "Check that one interactions is left");

        $actualInteraction1 = $actualInteractions[0];
        $this->assertEquals("Some types", $actualInteraction1->getProviderState(), "Some types");

        // empty case
        $json = '{"description":"A GET request","providerState":"Some types","request":{"method":"get","path":"/Call/","headers":{"Content-Type":"application/json"}},"response":{"status":200,"headers":{"Content-Type":"application/json"},"body":{"types":[{"id":1000}]}}}';
        $interaction1 = \json_decode($json);

        $json = '{"description":"Another GET request","providerState":"Some more types","request":{"method":"get","path":"/Call/","headers":{"Content-Type":"application/json"}},"response":{"status":200,"headers":{"Content-Type":"application/json"},"body":{"types":[{"id":1000}]}}}';
        $interaction2 = \json_decode($json);

        $expectedInteractions = array();
        $expectedInteractions[] = $interaction1;
        $expectedInteractions[] = $interaction2;

        $pactFile = new ProviderServicePactFile();
        $pactFile->setInteractions($expectedInteractions);
        $pactFile->filterInteractionsByProviderState("None existent state");
        $actualInteractions = $pactFile->getInteractions();

        $this->assertEquals(0, count($actualInteractions), "No interactions returned");
    }

    public function testSetInteractions()
    {
        $pactFile = new ProviderServicePactFile();
        $actualInteractions = $pactFile->setInteractions(array());
        $this->assertEquals(0, count($actualInteractions), "Empty array is allowed as there are no interactions");


        $json = '{"description":"A GET request","providerState":"Some types","request":{"method":"get","path":"/Call/","headers":{"Content-Type":"application/json"}},"response":{"status":200,"headers":{"Content-Type":"application/json"},"body":{"types":[{"id":1000}]}}}';
        $interaction1 = \json_decode($json);

        $json = '{"description":"Another GET request","providerState":"Some more types","request":{"method":"get","path":"/Call/","headers":{"Content-Type":"application/json"}},"response":{"status":200,"headers":{"Content-Type":"application/json"},"body":{"types":[{"id":1000}]}}}';
        $interaction2 = \json_decode($json);

        $expectedInteractions = array();
        $expectedInteractions[] = $interaction1;
        $expectedInteractions[] = $interaction2;

        $actualInteractions = $pactFile->setInteractions($expectedInteractions);


        $this->assertEquals(2, count($actualInteractions), "Check that two interactions were added");

        $actualInteractions = $pactFile->getInteractions();
        $this->assertEquals(2, count($actualInteractions), "Check that the getter works");

        $actualInteraction1 = $actualInteractions[0];
        $this->assertEquals("A GET request", $actualInteraction1->getDescription(), "Check that an interaction has a description");
        $this->assertEquals("Some types", $actualInteraction1->getProviderState(), "Check that an interaction has a provider state");
    }
}

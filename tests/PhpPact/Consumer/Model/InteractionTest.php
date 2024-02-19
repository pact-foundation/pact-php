<?php

namespace PhpPactTest\Consumer\Model;

use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Consumer\Model\ProviderState;
use PHPUnit\Framework\TestCase;

class InteractionTest extends TestCase
{
    public function testSetters()
    {
        $id                  = 123;
        $description         = 'a message';
        $providerStateName   = 'a provider state';
        $providerStateParams = ['foo' => 'bar'];
        $request             = new ConsumerRequest();
        $response            = new ProviderResponse();

        $subject = (new Interaction())
            ->setId($id)
            ->setDescription($description)
            ->addProviderState($providerStateName, $providerStateParams)
            ->setRequest($request)
            ->setResponse($response);

        static::assertSame($id, $subject->getId());
        static::assertSame($description, $subject->getDescription());
        $providerStates = $subject->getProviderStates();
        static::assertCount(1, $providerStates);
        static::assertContainsOnlyInstancesOf(ProviderState::class, $providerStates);
        static::assertEquals($providerStateName, $providerStates[0]->getName());
        static::assertEquals($providerStateParams, $providerStates[0]->getParams());
        static::assertSame($request, $subject->getRequest());
        static::assertSame($response, $subject->getResponse());
    }
}

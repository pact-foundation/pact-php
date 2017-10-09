<?php

namespace PhpPact\Mocks\MockHttpService\Mappers;

class ProviderServicePactMapper implements \PhpPact\Mappers\IMapper
{
    /**
     *
     * @param \stdClass $json
     * @return \PhpPact\Mocks\MockHttpService\Models\ProviderServicePactFile
     */
    public function Convert($request)
    {
        if (is_string($request)) {
            $request = \json_decode($request);
        }

        $pact = new \PhpPact\Mocks\MockHttpService\Models\ProviderServicePactFile();

        $provider = new \PhpPact\Models\Pacticipant();
        $provider->setName($request->provider);
        $pact->setProvider($provider);

        $consumer = new \PhpPact\Models\Pacticipant();
        $consumer->setName($request->consumer);
        $pact->setConsumer($consumer);

        $pact->setMetaData($request->metadata);
        $pact->setInteractions($request->interactions);

        return $pact;
    }
}

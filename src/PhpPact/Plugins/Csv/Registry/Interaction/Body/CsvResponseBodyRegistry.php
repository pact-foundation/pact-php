<?php

namespace PhpPact\Plugins\Csv\Registry\Interaction\Body;

use PhpPact\Consumer\Registry\Interaction\InteractionRegistryInterface;
use PhpPact\Consumer\Registry\Interaction\Part\ResponsePartTrait;
use PhpPact\FFI\ClientInterface;
use PhpPact\Plugin\Registry\Interaction\Body\AbstractPluginBodyRegistry;

class CsvResponseBodyRegistry extends AbstractPluginBodyRegistry
{
    use ResponsePartTrait;

    public function __construct(
        protected ClientInterface $client,
        private InteractionRegistryInterface $interactionRegistry
    ) {
        parent::__construct($client);
    }

    protected function getInteractionId(): int
    {
        return $this->interactionRegistry->getId();
    }
}

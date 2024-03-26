<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Consumer\Model\Interaction;
use PhpPactTest\CompatibilitySuite\Exception\UndefinedInteractionException;

final class InteractionsStorage implements InteractionsStorageInterface
{
    /**
     * @var array<int, Interaction>
     */
    private array $interactions = [];

    public function add(string $domain, int $id, Interaction $interaction, bool $clone = false): void
    {
        $this->interactions[$domain][$id] = $clone ? $this->cloneInteraction($interaction) : $interaction;
    }

    public function get(string $domain, int $id): Interaction
    {
        if (!isset($this->interactions[$domain][$id])) {
            throw new UndefinedInteractionException(sprintf('Interaction %s is not defined in domain %s', $id, $domain));
        }

        return $this->interactions[$domain][$id];
    }

    private function cloneInteraction(Interaction $interaction): Interaction
    {
        $result = clone $interaction;
        $result->setRequest(clone $interaction->getRequest());
        if ($interaction->getRequest()->getBody()) {
            $result->getRequest()->setBody(clone $interaction->getRequest()->getBody());
        }
        $result->setResponse(clone $interaction->getResponse());
        if ($interaction->getResponse()->getBody()) {
            $result->getResponse()->setBody(clone $interaction->getResponse()->getBody());
        }

        return $result;
    }
}

<?php

namespace PhpPactTest\CompatibilitySuite\Context\Shared\Transform;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use PhpPact\Consumer\Model\Interaction;
use PhpPactTest\CompatibilitySuite\Service\InteractionBuilderInterface;
use PhpPactTest\CompatibilitySuite\Service\MatchingRulesStorageInterface;

class InteractionsContext implements Context
{
    public function __construct(
        private InteractionBuilderInterface $builder,
        private MatchingRulesStorageInterface $matchingRulesStorage,
    ) {
    }

    /**
     * @Transform table:No,method,path,query,headers,body,response,response content,response body
     * @Transform table:No,method,path,query,headers,body,response,response headers,response content,response body
     * @Transform table:No,method,path,query,headers,body,matching rules
     * @Transform table:No,method,path,response,response headers,response content,response body,response matching rules
     *
     * @return array<int, Interaction>
     */
    public function getInteractions(TableNode $table): array
    {
        $interactions = [];
        foreach ($table->getHash() as $data) {
            $id = (int) $data['No'];
            $interactions[$id] = $this->builder->build($data);
            $this->storeMatchingRules($id, $data);
        }

        return $interactions;
    }

    private function storeMatchingRules(int $id, array $data): void
    {
        if (isset($data['matching rules'])) {
            $this->matchingRulesStorage->add(MatchingRulesStorageInterface::REQUEST_DOMAIN, $id, $data['matching rules']);
        }
        if (isset($data['response matching rules'])) {
            $this->matchingRulesStorage->add(MatchingRulesStorageInterface::RESPONSE_DOMAIN, $id, $data['response matching rules']);
        }
    }
}

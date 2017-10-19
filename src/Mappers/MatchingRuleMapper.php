<?php

namespace PhpPact\Mappers;

use PhpPact\Matchers\Rules\MatcherRuleTypes;
use PhpPact\Matchers\Rules\MatchingRule;

class MatchingRuleMapper
{

    /**
     * @param $obj \stdClass
     */
    public function convert($obj)
    {
        $matchingRules = array();
        if (isset($obj->matchingRules)
            && (
                (is_object($obj->matchingRules) || is_array($obj->matchingRules))
                && count($obj->matchingRules) > 0
            )
        ) {
            foreach ($obj->matchingRules as $jsonPath => $matchingRule) {
                $options = array();
                if (isset($matchingRule->min)) {
                    $options[MatcherRuleTypes::MIN_COUNT] = $matchingRule->min;
                }

                if (isset($matchingRule->match)) {
                    $options[MatcherRuleTypes::RULE_TYPE] = $matchingRule->match;
                }

                if (isset($matchingRule->regex)) {
                    $options[MatcherRuleTypes::REGEX_PATTERN] = $matchingRule->regex;
                }

                $matchingRuleVo = new MatchingRule($jsonPath, $options);
                $matchingRules[$matchingRuleVo->getJsonPath()] = $matchingRuleVo;
            }
        }

        return $matchingRules;
    }
}
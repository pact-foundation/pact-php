<?php

namespace PhpPact\Consumer\Model;

use PhpPact\Consumer\Model\Interaction\BodyTrait;
use PhpPact\Consumer\Model\Interaction\HeadersTrait;
use PhpPact\Consumer\Model\Interaction\StatusTrait;

/**
 * Response expectation that would be in response to a Consumer request from the Provider.
 */
class ProviderResponse
{
    use HeadersTrait;
    use BodyTrait;
    use StatusTrait;
}

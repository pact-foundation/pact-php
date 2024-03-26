<?php

namespace PhpPact\Consumer\Model;

use PhpPact\Consumer\Model\Interaction\BodyTrait;
use PhpPact\Consumer\Model\Interaction\HeadersTrait;
use PhpPact\Consumer\Model\Interaction\MethodTrait;
use PhpPact\Consumer\Model\Interaction\PathTrait;
use PhpPact\Consumer\Model\Interaction\QueryTrait;

/**
 * Request initiated by the consumer.
 */
class ConsumerRequest
{
    use HeadersTrait;
    use BodyTrait;
    use MethodTrait;
    use PathTrait;
    use QueryTrait;
}

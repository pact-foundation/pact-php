<?php

namespace PhpPact\Consumer\Registry\Interaction\Part;

use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Multipart;
use PhpPact\Consumer\Model\Body\Text;

interface PartRegistryInterface
{
    public function withBody(Text|Binary|Multipart|null $body): self;

    /**
     * @param array<string, string[]> $headers
     */
    public function withHeaders(array $headers): self;
}

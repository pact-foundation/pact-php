<?php

namespace PhpPact\Standalone\ProviderVerifier\Model;

use PhpPact\Standalone\ProviderVerifier\Model\Config\ConsumerFilters;
use PhpPact\Standalone\ProviderVerifier\Model\Config\FilterInfo;
use PhpPact\Standalone\ProviderVerifier\Model\Config\ProviderInfo;
use PhpPact\Standalone\ProviderVerifier\Model\Config\ProviderState;
use PhpPact\Standalone\ProviderVerifier\Model\Config\PublishOptions;
use PhpPact\Standalone\ProviderVerifier\Model\Config\VerificationOptions;

/**
 * {@inheritdoc}
 */
class VerifierConfig implements VerifierConfigInterface
{
    use ProviderInfo;
    use FilterInfo;
    use ProviderState;
    use VerificationOptions;
    use PublishOptions;
    use ConsumerFilters;
}

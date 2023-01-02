<?php

namespace PhpPact\Standalone\ProviderVerifier\Model;

use PhpPact\Standalone\ProviderVerifier\Model\Config\ConsumerFiltersInterface;
use PhpPact\Standalone\ProviderVerifier\Model\Config\FilterInfoInterface;
use PhpPact\Standalone\ProviderVerifier\Model\Config\ProviderInfoInterface;
use PhpPact\Standalone\ProviderVerifier\Model\Config\ProviderStateInterface;
use PhpPact\Standalone\ProviderVerifier\Model\Config\PublishOptionsInterface;
use PhpPact\Standalone\ProviderVerifier\Model\Config\VerificationOptionsInterface;

/**
 * Interface VerifierConfigInterface.
 */
interface VerifierConfigInterface extends ProviderInfoInterface, FilterInfoInterface, ProviderStateInterface, VerificationOptionsInterface, PublishOptionsInterface, ConsumerFiltersInterface
{
}

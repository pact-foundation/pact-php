<?php

namespace PhpPactTest\CompatibilitySuite\Model\Mismatch;

/**
 * The type of a mismatch, as serialized by the verifier (the `type` field of
 * a Mismatch, see pact-reference/rust/pact_matching/src/lib.rs).
 */
enum VerifierMismatchType: string
{
    case METHOD = 'MethodMismatch';
    case PATH = 'PathMismatch';
    case STATUS = 'StatusMismatch';
    case QUERY = 'QueryMismatch';
    case HEADER = 'HeaderMismatch';
    case BODY_TYPE = 'BodyTypeMismatch';
    case BODY = 'BodyMismatch';
    case METADATA = 'MetadataMismatch';

    /**
     * The human readable description asserted against the verification results,
     * or null when the mismatch type is not described by a fixed message.
     */
    public function description(): ?string
    {
        return match ($this) {
            self::STATUS => 'Response status did not match',
            self::HEADER => 'Headers had differences',
            self::BODY_TYPE => 'Body type had differences',
            self::BODY => 'Body had differences',
            self::METADATA => 'Metadata had differences',
            self::METHOD, self::PATH, self::QUERY => null,
        };
    }
}

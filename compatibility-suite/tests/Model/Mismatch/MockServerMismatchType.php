<?php

namespace PhpPactTest\CompatibilitySuite\Model\Mismatch;

/**
 * The type of a mismatch, as serialized by the mock server (the `type` field
 * of a Mismatch, see pact-reference/rust/pact_matching/src/lib.rs).
 */
enum MockServerMismatchType: string
{
    case METHOD = 'method';
    case PATH = 'path';
    case STATUS = 'status';
    case QUERY = 'query';
    case HEADER = 'header';
    case BODY_CONTENT_TYPE = 'body-content-type';
    case BODY = 'body';
    case METADATA = 'metadata';

    public function verifierType(): VerifierMismatchType
    {
        return match ($this) {
            self::METHOD => VerifierMismatchType::METHOD,
            self::PATH => VerifierMismatchType::PATH,
            self::STATUS => VerifierMismatchType::STATUS,
            self::QUERY => VerifierMismatchType::QUERY,
            self::HEADER => VerifierMismatchType::HEADER,
            self::BODY_CONTENT_TYPE => VerifierMismatchType::BODY_TYPE,
            self::BODY => VerifierMismatchType::BODY,
            self::METADATA => VerifierMismatchType::METADATA,
        };
    }
}

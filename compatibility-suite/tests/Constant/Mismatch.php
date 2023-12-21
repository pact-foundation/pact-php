<?php

namespace PhpPactTest\CompatibilitySuite\Constant;

class Mismatch
{
    public const VERIFIER_MISMATCH_TYPE_MAP = [
        'MethodMismatch' => false,
        'PathMismatch' => false,
        'StatusMismatch' => 'Response status did not match',
        'QueryMismatch' => false,
        'HeaderMismatch' => 'Headers had differences',
        'BodyTypeMismatch' => 'Body type had differences',
        'BodyMismatch' => 'Body had differences',
        'MetadataMismatch' => 'Metadata had differences',
    ];

    public const VERIFIER_MISMATCH_ERROR_MAP = [
        'One or more of the setup state change handlers has failed' => 'State change request failed',
    ];

    public const MOCK_SERVER_MISMATCH_TYPE_MAP = [
        'method' => 'MethodMismatch',
        'path' => 'PathMismatch',
        'status' => 'StatusMismatch',
        'query' => 'QueryMismatch',
        'header' => 'HeaderMismatch',
        'body-content-type' => 'BodyTypeMismatch',
        'body' => 'BodyMismatch',
        'metadata' => 'MetadataMismatch',
    ];
}

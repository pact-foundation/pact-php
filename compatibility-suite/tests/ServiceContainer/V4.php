<?php

namespace PhpPactTest\CompatibilitySuite\ServiceContainer;

use PhpPactTest\CompatibilitySuite\Service\SyncMessagePactWriter;

class V4 extends V3
{
    public function __construct()
    {
        parent::__construct();
        $this->set('sync_message_pact_writer', new SyncMessagePactWriter($this->getSpecification()));
    }

    protected function getSpecification(): string
    {
        return '4.0.0';
    }
}

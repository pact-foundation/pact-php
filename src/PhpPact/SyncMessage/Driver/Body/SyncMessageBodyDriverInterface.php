<?php

namespace PhpPact\SyncMessage\Driver\Body;

use PhpPact\SyncMessage\Model\SyncMessage;

interface SyncMessageBodyDriverInterface
{
    public function registerBody(SyncMessage $message): void;
}

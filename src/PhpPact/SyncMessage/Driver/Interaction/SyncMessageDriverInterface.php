<?php

namespace PhpPact\SyncMessage\Driver\Interaction;

use PhpPact\Standalone\MockService\Model\VerifyResult;
use PhpPact\SyncMessage\Model\SyncMessage;

interface SyncMessageDriverInterface
{
    public function registerMessage(SyncMessage $message): void;

    public function verifyMessage(): VerifyResult;

    public function writePactAndCleanUp(): void;
}

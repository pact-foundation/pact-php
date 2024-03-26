<?php

namespace ProtobufSyncMessageProvider\Service;

use Plugins\ShapeMessage;
use Plugins\AreaResponse;
use Spiral\RoadRunner\GRPC\ContextInterface;
use Spiral\RoadRunner\GRPC\ServiceInterface;

interface CalculatorInterface extends ServiceInterface
{
    public const NAME = "plugins.Calculator";

    public function calculate(ContextInterface $context, ShapeMessage $request): AreaResponse;
}

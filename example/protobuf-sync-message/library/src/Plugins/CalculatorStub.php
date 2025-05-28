<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Plugins;

/**
 */
class CalculatorStub {

    /**
     * @param \Plugins\ShapeMessage $request client request
     * @param \Grpc\ServerContext $context server request context
     * @return \Plugins\AreaResponse for response data, null if if error occurred
     *     initial metadata (if any) and status (if not ok) should be set to $context
     */
    public function calculate(
        \Plugins\ShapeMessage $request,
        \Grpc\ServerContext $context
    ): ?\Plugins\AreaResponse {
        $context->setStatus(\Grpc\Status::unimplemented());
        return null;
    }

    /**
     * Get the method descriptors of the service for server registration
     *
     * @return array of \Grpc\MethodDescriptor for the service methods
     */
    public final function getMethodDescriptors(): array
    {
        return [
            '/plugins.Calculator/calculate' => new \Grpc\MethodDescriptor(
                $this,
                'calculate',
                '\Plugins\ShapeMessage',
                \Grpc\MethodDescriptor::UNARY_CALL
            ),
        ];
    }

}

<?php

namespace ProtobufSyncMessageProvider\Service;

use Grpc\ServerContext;
use Plugins\AreaResponse;
use Plugins\CalculatorStub;
use Plugins\Circle;
use Plugins\Parallelogram;
use Plugins\Rectangle;
use Plugins\ShapeMessage;
use Plugins\Square;
use Plugins\Triangle;
use Exception;

class Calculator extends CalculatorStub
{
    public function calculate(ShapeMessage $request, ServerContext $serverContext): AreaResponse
    {
        if (empty($request->getCreated())) {
            throw new Exception('Shape created date is required');
        }
        if (empty($request->getId())) {
            throw new Exception('Shape ID is required');
        }
        switch ($request->getShape()) {
            case 'square':
                $area = $this->calculateSquareArea($request->getSquare());
                break;
            case 'rectangle':
                $area = $this->calculateRectangleArea($request->getRectangle());
                break;
            case 'circle':
                $area = $this->calculateCircleArea($request->getCircle());
                break;
            case 'triangle':
                $area = $this->calculateTriangleArea($request->getTriangle());
                break;
            case 'parallelogram':
                $area = $this->calculateParallelogramArea($request->getParallelogram());
                break;
            default:
                throw new Exception(sprintf('Shape %s is not supported', $request->getShape()));
        }

        return new AreaResponse(['value' => $area]);
    }

    private function calculateSquareArea(Square $square): float
    {
        return pow($square->getEdgeLength(), 2);
    }

    private function calculateRectangleArea(Rectangle $rectangle): float
    {
        return $rectangle->getWidth() * $rectangle->getLength();
    }

    private function calculateCircleArea(Circle $circle): float
    {
        return pi() * pow($circle->getRadius(), 2);
    }

    /**
     * Use Heron's formula.
     */
    private function calculateTriangleArea(Triangle $triangle): float
    {
        $p = ($triangle->getEdgeA() + $triangle->getEdgeB() + $triangle->getEdgeC()) / 2;

        return sqrt($p * ($p - $triangle->getEdgeA()) * ($p - $triangle->getEdgeB()) * ($p - $triangle->getEdgeC()));
    }

    private function calculateParallelogramArea(Parallelogram $parallelogram): float
    {
        return $parallelogram->getBaseLength() * $parallelogram->getHeight();
    }
}

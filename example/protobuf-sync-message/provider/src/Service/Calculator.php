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
                $square = $request->getSquare();
                if ($square === null) {
                    throw new Exception('Square is required');
                }
                $area = $this->calculateSquareArea($square);
                break;
            case 'rectangle':
                $rectangle = $request->getRectangle();
                if ($rectangle === null) {
                    throw new Exception('Rectangle is required');
                }
                $area = $this->calculateRectangleArea($rectangle);
                break;
            case 'circle':
                $circle = $request->getCircle();
                if ($circle === null) {
                    throw new Exception('Circle is required');
                }
                $area = $this->calculateCircleArea($circle);
                break;
            case 'triangle':
                $triangle = $request->getTriangle();
                if ($triangle === null) {
                    throw new Exception('Triangle is required');
                }
                $area = $this->calculateTriangleArea($triangle);
                break;
            case 'parallelogram':
                $parallelogram = $request->getParallelogram();
                if ($parallelogram === null) {
                    throw new Exception('Parallelogram is required');
                }
                $area = $this->calculateParallelogramArea($parallelogram);
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

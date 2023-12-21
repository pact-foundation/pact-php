<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use JsonException;
use PhpPactTest\CompatibilitySuite\Constant\Path;
use PhpPactTest\CompatibilitySuite\Exception\FixtureNotFoundException;
use PhpPactTest\CompatibilitySuite\Exception\InvalidJsonFixtureException;

class FixtureLoader implements FixtureLoaderInterface
{
    public function load(string $fileName): string
    {
        return file_get_contents($this->getFilePath($fileName));
    }

    public function loadJson(string $fileName): mixed
    {
        try {
            return json_decode($this->load($fileName), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new InvalidJsonFixtureException(sprintf("Could not load json fixture '%s': %s", $fileName, $exception->getMessage()));
        }
    }

    public function isBinary(string $fileName): bool
    {
        $ext = pathinfo($this->getFilePath($fileName), PATHINFO_EXTENSION);

        // TODO Find a better way
        return in_array($ext, ['jpg', 'pdf']);
    }

    public function determineContentType(string $fileName): string
    {
        if (str_ends_with($fileName, '.json')) {
            return 'application/json';
        } elseif (str_ends_with($fileName, '.xml')) {
            return 'application/xml';
        } elseif (str_ends_with($fileName, '.jpg')) {
            return 'image/jpeg';
        } elseif (str_ends_with($fileName, '.pdf')) {
            return 'application/pdf';
        } else {
            return 'text/plain';
        }
    }

    public function getFilePath(string $fileName): string
    {
        $filePath = Path::FIXTURES_PATH . '/' . $fileName;
        if (!file_exists($filePath)) {
            throw new FixtureNotFoundException(sprintf("Could not load fixture '%s'", $fileName));
        }

        return $filePath;
    }
}

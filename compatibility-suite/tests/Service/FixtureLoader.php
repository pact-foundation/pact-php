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
        $contents = file_get_contents($this->getFilePath($fileName));
        if (false === $contents) {
            throw new FixtureNotFoundException(sprintf("Could not load fixture '%s'", $fileName));
        }

        return $contents;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function loadJson(string $fileName): array
    {
        try {
            $decoded = json_decode($this->load($fileName), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new InvalidJsonFixtureException(sprintf("Could not load json fixture '%s': %s", $fileName, $exception->getMessage()));
        }
        if (!is_array($decoded)) {
            throw new InvalidJsonFixtureException(sprintf("Could not load json fixture '%s': not an array", $fileName));
        }

        return $decoded;
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

<?php

namespace PhpPact\Mocks\MockHttpService\Validators;

use JsonSchema\Validator;
use PhpPact\Models\PactFile;
use PhpPact\PactFailureException;

/**
 * Class PactFileValidator
 *
 * Validate the well formed nature of the pact file before it is written.
 */
class PactFileValidator
{
    /**
     * @param PactFile $pactFile
     *
     * @throws PactFailureException
     *
     * @return bool
     */
    public function validate(PactFile $pactFile)
    {
        $data = \json_decode(\json_encode($pactFile));

        $validator = new Validator;
        $validator->validate($data, (object) ['$ref' => 'file://' . $this->huntForSchema($pactFile)]);

        if (!$validator->isValid()) {
            $msg = "JSON does not validate. Violations:\n";
            foreach ($validator->getErrors() as $error) {
                $msg .= \sprintf("[%s] %s\n", $error['property'], $error['message']);
            }

            throw new PactFailureException($msg);
        }

        return true;
    }

    private function huntForSchema(PactFile $pactFile)
    {
        $fileName    = 'pact-file-schema.json';
        $currentDir  = __DIR__;
        $relativeDir = $currentDir . '/../../../Schema/' . $pactFile->getPactSpecificationVersion() . '/' . $fileName;
        $realPath    = \realpath($relativeDir);

        if (!\file_exists($realPath)) {
            throw new \Exception(\sprintf('Schema for Pact File cannot be found: %s', $relativeDir));
        }

        return $realPath;
    }
}

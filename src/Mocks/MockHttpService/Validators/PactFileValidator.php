<?php

namespace PhpPact\Mocks\MockHttpService\Validators;

/**
 * Class PactFileValidator
 *
 * Validate the well formed nature of the pact file before it is written.
 */
class PactFileValidator
{

    /**
     * @param \PhpPact\Models\PactFile $pactFile
     * @return bool
     * @throws \PhpPact\PactFailureException
     */
    public function validate(\PhpPact\Models\PactFile $pactFile)
    {
        $data = json_decode(json_encode($pactFile));

        $validator = new \JsonSchema\Validator;
        $validator->validate($data, (object)['$ref' => 'file://' . $this->HuntForSchema()]);

        if (!$validator->isValid()) {
            $msg = "JSON does not validate. Violations:\n";
            foreach ($validator->getErrors() as $error) {
                $msg .= sprintf("[%s] %s\n", $error['property'], $error['message']);
            }

            throw new \PhpPact\PactFailureException($msg);
        }

        return true;
    }

    private function HuntForSchema()
    {
        $fileName = 'pact-file-schema.json';
        $currentDir = dirname(__FILE__);
        $relativeDir = $currentDir . '/../../../Models/' . $fileName;
        $realPath = realpath($relativeDir);
        return $realPath;
    }
}

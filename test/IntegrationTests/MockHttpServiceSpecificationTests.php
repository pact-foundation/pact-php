<?php

namespace PhpPactTest\IntegrationTests;

use PHPUnit\Framework\TestCase;

class MockHttpServiceSpecificationTests extends TestCase
{

    /**
     * @test
     */
    public function TestRequestSpecification()
    {
        $this->RunPactSpecificationTests(__DIR__ . '/testcases-v2_0/request', 'request');
    }


    /**
     * @test
     */
    public function TestResponseSpecification()
    {
        $this->RunPactSpecificationTests(__DIR__ . '/testcases-v2_0/response', 'response');
    }

    private function RunPactSpecificationTests($pathToTestCases, $testCaseType)
    {
        if (!is_dir($pathToTestCases)) {
            throw new \InvalidArgumentException(sprintf("Specification tests not found in path '%s'", $pathToTestCases));
        }

        $testCaseSubDirectories = $this->GetDirectories($pathToTestCases);
        if (count($testCaseSubDirectories)) {
            foreach ($testCaseSubDirectories as $testCaseSubDirectory) {
                $testCaseFileNames = $this->GetFiles($pathToTestCases . DIRECTORY_SEPARATOR . $testCaseSubDirectory);
                foreach ($testCaseFileNames as $testCaseFileName) {
                    $fullPathFileName = $pathToTestCases . DIRECTORY_SEPARATOR . $testCaseSubDirectory . DIRECTORY_SEPARATOR . $testCaseFileName;
                    $testCaseJson = file_get_contents($fullPathFileName);


                    error_log("Starting " . $fullPathFileName);

                    if ($testCaseFileName == 'objects in array with type mismatching.json' && $testCaseType == 'response') {
                        error_log("Debug " . $fullPathFileName);
                    }
                    if ($testCaseFileName == 'array size less than required.json' && $testCaseType == 'request') {
                        error_log("Debug " . $fullPathFileName);
                    }


                    $hasException = false;

                    try {
                        switch ($testCaseType) {
                            case 'request':
                                $testCaseRunner = new Models\RequestTestCase();
                                $testCaseRunner->initialize($testCaseJson);
                                break;
                            case 'response':
                                $testCaseRunner = new Models\ResponseTestCase();
                                $testCaseRunner->initialize($testCaseJson);
                                break;
                            default:
                                throw new \InvalidArgumentException("Unexpected test case runner type: " . $testCaseType);
                        }

                        $testCaseRunner->verify($fullPathFileName);
                    } catch (\Exception $e) {
                        $hasException = true;
                    }
                    $this->assertFalse($hasException, "Expected to pass case from: " . $testCaseSubDirectory . DIRECTORY_SEPARATOR . $testCaseFileName);
                }
            }
        }
    }

    /**
     * Get the list of directories
     *
     * @param $dir
     * @return array
     */
    private function GetDirectories($dir)
    {
        $result = array();

        $cdir = scandir($dir);
        foreach ($cdir as $key => $value) {
            if (!in_array($value, array(".", ".."))) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $result[$value] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * Get the list of files
     *
     * @param $dir
     * @return array
     */
    private function GetFiles($dir)
    {
        $result = array();

        $cdir = scandir($dir);
        foreach ($cdir as $key => $value) {
            if (!in_array($value, array(".", ".."))) {
                if (!is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $result[$value] = $value;
                }
            }
        }

        return $result;
    }
}

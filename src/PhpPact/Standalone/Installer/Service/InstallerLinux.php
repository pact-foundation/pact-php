<?php

namespace PhpPact\Standalone\Installer\Service;

use PhpPact\Standalone\Installer\Exception\FileDownloadFailureException;
use PhpPact\Standalone\Installer\Model\Scripts;

class InstallerLinux implements InstallerInterface
{
    const VERSION = '1.87.5';

    /**
     * {@inheritdoc}
     */
    public function isEligible(): bool
    {
        return PHP_OS === 'Linux';
    }

    /**
     * {@inheritdoc}
     */
    public function install(string $destinationDir): Scripts
    {
        if (\file_exists($destinationDir . DIRECTORY_SEPARATOR . 'pact') === false) {
            $fileName     = 'pact-' . self::VERSION . '-linux-x86_64.tar.gz';
            $tempFilePath = \sys_get_temp_dir() . DIRECTORY_SEPARATOR . $fileName;

            $this
                ->download($fileName, $tempFilePath)
                ->extract($tempFilePath, $destinationDir)
                ->deleteCompressed($tempFilePath);
        }

        $scripts = new Scripts(
            "{$destinationDir}/pact/bin/pact-mock-service",
            "{$destinationDir}/pact/bin/pact-stub-service",
            "{$destinationDir}/pact/bin/pact-provider-verifier",
            "{$destinationDir}/pact/bin/pact-message"
        );

        return $scripts;
    }

    /**
     * Download the binaries.
     *
     * @param string $fileName     name of the file to be downloaded
     * @param string $tempFilePath location to download the file
     *
     * @throws FileDownloadFailureException
     *
     * @return InstallerLinux
     */
    private function download(string $fileName, string $tempFilePath): self
    {
        $uri = 'https://github.com/pact-foundation/pact-ruby-standalone/releases/download/v' . self::VERSION . "/{$fileName}";

        $data = \file_get_contents($uri);

        if ($data === false) {
            throw new FileDownloadFailureException('Failed to download binary from Github for Ruby Standalone!');
        }

        $result = \file_put_contents($tempFilePath, $data);

        if ($result === false) {
            throw new FileDownloadFailureException('Failed to save binaries for Ruby Standalone!');
        }

        return $this;
    }

    /**
     * Uncompress the temp file and install the binaries in the destination directory.
     *
     * @param string $sourceFile
     * @param string $destinationDir
     *
     * @return InstallerLinux
     * @return string
     */
    private function extract(string $sourceFile, string $destinationDir): self
    {
        $p = new \PharData($sourceFile);
        $p->extractTo($destinationDir);

        return $this;
    }

    /**
     * Delete the temp file.
     *
     * @param string $filePath
     *
     * @return InstallerLinux
     */
    private function deleteCompressed(string $filePath): self
    {
        \unlink($filePath);

        return $this;
    }
}

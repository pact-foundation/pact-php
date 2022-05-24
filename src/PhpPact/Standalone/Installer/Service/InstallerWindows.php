<?php

namespace PhpPact\Standalone\Installer\Service;

use PhpPact\Standalone\Installer\Exception\FileDownloadFailureException;
use PhpPact\Standalone\Installer\Model\Scripts;
use ZipArchive;

/**
 * Download the Ruby Standalone binaries for Windows.
 * Class BinaryDownloaderWindows.
 */
class InstallerWindows implements InstallerInterface
{
    public const VERSION = '1.88.83';

    /**
     * {@inheritdoc}
     */
    public function isEligible(): bool
    {
        return PHP_OS_FAMILY === 'Windows';
    }

    /**
     * {@inheritdoc}
     */
    public function install(string $destinationDir): Scripts
    {
        if (\file_exists($destinationDir . DIRECTORY_SEPARATOR . 'pact') === false) {
            $fileName     = 'pact-' . self::VERSION . '-win32.zip';
            $tempFilePath = __DIR__ . DIRECTORY_SEPARATOR . $fileName;

            $this
                ->download($fileName, $tempFilePath)
                ->extract($tempFilePath, $destinationDir)
                ->deleteCompressed($tempFilePath);
        }

        $binDir  = $destinationDir . DIRECTORY_SEPARATOR . 'pact' . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR;
        $scripts = new Scripts(
            $binDir . 'pact-mock-service.bat',
            $binDir . 'pact-stub-service.bat',
            $binDir . 'pact-provider-verifier.bat',
            $binDir . 'pact-message.bat',
            $binDir . 'pact-broker.bat'
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
     * @return InstallerWindows
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
     * @return InstallerWindows
     */
    private function extract(string $sourceFile, string $destinationDir): self
    {
        $zip = new ZipArchive();

        if ($zip->open($sourceFile)) {
            $zip->extractTo($destinationDir);
            $zip->close();
        }

        return $this;
    }

    /**
     * Delete the temp file.
     *
     * @param string $filePath
     *
     * @return InstallerWindows
     */
    private function deleteCompressed(string $filePath): self
    {
        \unlink($filePath);

        return $this;
    }
}

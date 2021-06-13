<?php

namespace PhpPact\Standalone\Installer\Service;

use PhpPact\Standalone\Installer\Exception\FileDownloadFailureException;
use PhpPact\Standalone\Installer\Model\Scripts;
use ZipArchive;

/**
 * Class AbstractInstaller.
 */
abstract class AbstractInstaller implements InstallerInterface
{
    public const PACT_RUBY_STANDALONE_VERSION = '1.88.83';
    public const PACT_FFI_VERSION             = '0.2.6';
    public const PACT_STUB_SERVER_VERSION     = '0.4.4';

    public const FILES = [
        [
            'repo'          => 'pact-reference',
            'filename'      => 'pact.h',
            'version'       => self::PACT_FFI_VERSION,
            'versionPrefix' => 'libpact_ffi-v',
            'extract'       => false,
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function install(string $destinationDir): Scripts
    {
        if (\file_exists(($destinationDir)) === false) {
            \mkdir($destinationDir);

            foreach (static::FILES as $file) {
                $uri = \sprintf(
                    'https://github.com/pact-foundation/%s/releases/download/%s%s/%s',
                    $file['repo'],
                    $file['versionPrefix'],
                    $file['version'],
                    $file['filename']
                );
                $tempFilePath = $destinationDir . DIRECTORY_SEPARATOR . $file['filename'];

                $this->download($uri, $tempFilePath);
                if ($file['extract']) {
                    $this
                        ->extract($tempFilePath, $destinationDir, $file['extractTo'] ?? null, $file['executable'] ?? false)
                        ->deleteCompressed($tempFilePath);
                }
            }
        }

        return $this->getScripts($destinationDir);
    }

    /**
     * @param string $destinationDir
     *
     * @return Scripts
     */
    abstract protected function getScripts(string $destinationDir): Scripts;

    /**
     * Download the binaries.
     *
     * @param string $uri          uri of the file to be downloaded
     * @param string $tempFilePath location to download the file
     *
     * @throws FileDownloadFailureException
     *
     * @return AbstractInstaller
     */
    private function download(string $uri, string $tempFilePath): self
    {
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
     * @param string      $sourceFile
     * @param string      $destinationDir
     * @param null|string $fileName       Required for gz file
     * @param bool        $executable     Required for gz file
     *
     * @return AbstractInstaller
     */
    private function extract(string $sourceFile, string $destinationDir, ?string $fileName, bool $executable): self
    {
        if (\substr($sourceFile, -7) === '.tar.gz') {
            $this->extractTarGz($sourceFile, $destinationDir);
        } elseif (\substr($sourceFile, -4) === '.zip') {
            $this->extractZip($sourceFile, $destinationDir);
        } else {
            $this->extractGz($sourceFile, $fileName, $executable);
        }

        return $this;
    }

    /**
     * @param string $sourceFile
     * @param string $destinationDir
     */
    private function extractTarGz(string $sourceFile, string $destinationDir): void
    {
        $p = new \PharData($sourceFile);
        $p->extractTo($destinationDir);
    }

    /**
     * @param string $sourceFile
     * @param string $destinationDir
     */
    private function extractZip(string $sourceFile, string $destinationDir): void
    {
        $zip = new ZipArchive();

        if ($zip->open($sourceFile)) {
            $zip->extractTo($destinationDir);
            $zip->close();
        }
    }

    /**
     * https://gist.github.com/bastiankoetsier/99827fa4754207d860ac
     *
     * @param string $sourceFile
     * @param string $fileName
     * @param bool   $executable
     */
    private function extractGz(string $sourceFile, string $fileName, bool $executable): void
    {
        $bufferSize = 4096;
        $destFile   = \pathinfo($sourceFile, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . $fileName;
        $file       = \gzopen($sourceFile, 'rb');
        $outFile    = \fopen($destFile, 'wb');
        while (!\gzeof($file)) {
            \fwrite($outFile, \gzread($file, $bufferSize));
        }
        \fclose($outFile);
        \gzclose($file);
        if ($executable) {
            \chmod($destFile, 0744);
        }
    }

    /**
     * Delete the temp file.
     *
     * @param string $filePath
     *
     * @return AbstractInstaller
     */
    private function deleteCompressed(string $filePath): self
    {
        \unlink($filePath);

        return $this;
    }
}

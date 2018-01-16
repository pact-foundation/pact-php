<?php

namespace PhpPact\Standalone\Installer\Service;

use PhpPact\Standalone\Installer\Exception\FileDownloadFailureException;
use PhpPact\Standalone\Installer\Model\Scripts;
use Symfony\Component\Filesystem\Filesystem;

class InstallerMac implements InstallerInterface
{
    /**
     * @inheritDoc
     */
    public function isEligible(): bool
    {
        return PHP_OS === 'Darwin';
    }

    /**
     * @inheritDoc
     */
    public function install(string $destinationDir): Scripts
    {
        $fs = new Filesystem();

        if ($fs->exists($destinationDir . DIRECTORY_SEPARATOR . 'pact') === false) {
            $version      = '1.22.1';
            $fileName     = "pact-{$version}-osx.tar.gz";
            $tempFilePath = \sys_get_temp_dir() . DIRECTORY_SEPARATOR . $fileName;

            $this
                ->download($fileName, $tempFilePath)
                ->extract($tempFilePath, $destinationDir)
                ->deleteCompressed($tempFilePath);
        }

        $scripts = new Scripts(
            "{$destinationDir}/pact/bin/pact-mock-service"
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
     * @return InstallerMac
     */
    private function download(string $fileName, string $tempFilePath): self
    {
        $uri  = "https://github.com/pact-foundation/pact-ruby-standalone/releases/download/v1.22.1/{$fileName}";
        $data = \file_get_contents($uri);

        $result = \file_put_contents($tempFilePath, $data);

        if ($result === false) {
            throw new FileDownloadFailureException('Failed to download file.');
        }

        return $this;
    }

    /**
     * Uncompress the temp file and install the binaries in the destination directory.
     *
     * @param string $sourceFile
     * @param string $destinationDir
     *
     * @return InstallerMac
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
     * @return InstallerMac
     */
    private function deleteCompressed(string $filePath): self
    {
        $fs = new Filesystem();
        $fs->remove($filePath);

        return $this;
    }
}

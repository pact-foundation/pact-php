<?php

namespace PhpPact\Standalone\Installer;

use PhpPact\Standalone\Installer\Exception\NoDownloaderFoundException;
use PhpPact\Standalone\Installer\Model\Scripts;
use PhpPact\Standalone\Installer\Service\InstallerInterface;
use PhpPact\Standalone\Installer\Service\InstallerLinux;
use PhpPact\Standalone\Installer\Service\InstallerMac;
use PhpPact\Standalone\Installer\Service\InstallerPosixPreinstalled;
use PhpPact\Standalone\Installer\Service\InstallerWindows;

/**
 * Manage Ruby Standalone binaries.
 * Class BinaryManager.
 */
class InstallManager
{
    /** @var InstallerInterface[] */
    private $installers = [];

    /**
     * Destination directory for PACT folder.
     *
     * @var string
     */
    private static $destinationDir = __DIR__ . '/../../../..';

    public function __construct()
    {
        $this
            ->registerInstaller(new InstallerWindows())
            ->registerInstaller(new InstallerMac())
            ->registerInstaller(new InstallerLinux())
            ->registerInstaller(new InstallerPosixPreinstalled());
    }

    /**
     * Add a single downloader.
     *
     * @param InstallerInterface $installer
     *
     * @return InstallManager
     */
    public function registerInstaller(InstallerInterface $installer): self
    {
        $this->installers[] = $installer;

        return $this;
    }

    /**
     * Overwrite default downloaders.
     *
     * @param array $installers
     *
     * @return InstallManager
     */
    public function setInstallers(array $installers): self
    {
        $this->installers = $installers;

        return $this;
    }

    /**
     * @throws Exception\FileDownloadFailureException
     * @throws NoDownloaderFoundException
     *
     * @return Scripts
     */
    public function install(): Scripts
    {
        $downloader = $this->getDownloader();

        return $downloader->install(self::$destinationDir);
    }

    /**
     * Uninstall.
     */
    public static function uninstall()
    {
        $pactInstallPath = self::$destinationDir . DIRECTORY_SEPARATOR . 'pact';
        if (\file_exists($pactInstallPath)) {
            self::rmdir($pactInstallPath);
        }
    }

    /**
     * Modified copy of Symphony filesystem remove
     *
     *
        Permission is hereby granted, free of charge, to any person obtaining a copy
        of this software and associated documentation files (the "Software"), to deal
        in the Software without restriction, including without limitation the rights
        to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
        copies of the Software, and to permit persons to whom the Software is furnished
        to do so, subject to the following conditions:

        The above copyright notice and this permission notice shall be included in all
        copies or substantial portions of the Software.

        THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
        IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
        FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
        AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
        LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
        OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
        THE SOFTWARE.

     * @param mixed $files
     *
     * @throws \Exception
     */
    public static function rmdir($files)
    {
        if ($files instanceof \Traversable) {
            $files = \iterator_to_array($files, false);
        } elseif (!\is_array($files)) {
            $files = [$files];
        }
        $files = \array_reverse($files);
        foreach ($files as $file) {
            if (\is_link($file)) {
                // See https://bugs.php.net/52176
                \unlink($file);

                if ('\\' !== \DIRECTORY_SEPARATOR || \file_exists($file)) {
                    throw new \Exception(\sprintf('Failed to remove symlink "%s"', $file));
                }
            } elseif (\is_dir($file)) {
                self::rmdir(new \FilesystemIterator($file, \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS));
                \rmdir($file);

                if (\file_exists($file)) {
                    throw new \Exception(\sprintf('Failed to remove directory "%s"', $file));
                }
            } else {
                \unlink($file);

                if (\file_exists($file)) {
                    throw new \Exception(\sprintf('Failed to remove file "%s"', $file));
                }
            }
        }
    }

    /**
     * Get the first downloader that meets the systems eligibility.
     *
     * @throws NoDownloaderFoundException
     *
     * @return InstallerInterface
     */
    private function getDownloader(): InstallerInterface
    {
        /**
         * Reverse the order of the downloaders so that the ones added last are checked first.
         *
         * @var InstallerInterface[]
         */
        $installers = \array_reverse($this->installers);
        foreach ($installers as $installer) {
            /** @var InstallerInterface $installer */
            if ($installer->isEligible()) {
                return $installer;
            }
        }

        throw new NoDownloaderFoundException('No eligible downloader found for Mock Server binaries.');
    }
}

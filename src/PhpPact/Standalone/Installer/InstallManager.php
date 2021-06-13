<?php

namespace PhpPact\Standalone\Installer;

use PhpPact\Standalone\Installer\Exception\FileDownloadFailureException;
use PhpPact\Standalone\Installer\Exception\NoDownloaderFoundException;
use PhpPact\Standalone\Installer\Model\Scripts;
use PhpPact\Standalone\Installer\Service\InstallerInterface;
use PhpPact\Standalone\Installer\Service\InstallerLinux;
use PhpPact\Standalone\Installer\Service\InstallerMac;
use PhpPact\Standalone\Installer\Service\InstallerWindows;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Manage Ruby Standalone binaries.
 * Class BinaryManager.
 */
class InstallManager
{
    /** @var InstallerInterface[] */
    private array $installers = [];

    /**
     * Destination directory for PACT folder.
     */
    private static string $destinationDir = __DIR__ . '/../../../../pact';

    public function __construct()
    {
        $this
            ->registerInstaller(new InstallerWindows())
            ->registerInstaller(new InstallerMac())
            ->registerInstaller(new InstallerLinux());
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
     * @throws FileDownloadFailureException
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
    public static function uninstall(): void
    {
        if (\file_exists(self::$destinationDir)) {
            (new Filesystem())->remove(self::$destinationDir);
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

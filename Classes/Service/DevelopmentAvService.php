<?php

namespace Extrameile\EmAntivirus\Service;

use TYPO3\CMS\Core\Utility\CommandUtility;

class DevelopmentAvService extends \TYPO3\CMS\Core\Service\AbstractService implements \Extrameile\EmAntivirus\Service\ScanServiceInterface
{
    /**
     * The extension key
     */
    public $extKey = 'em_antivirus';

    /**
     * Scans the given file
     *
     * @param string $filePathAndName Complete path and filename of the file
     *
     * @return string[] Array of error messages from scanner
     */
    public function scanFile($filePathAndName): array
    {
        if ($this->hasVirusInContent($filePathAndName)) {
            return ['String virus detected in file/path name'];
        }

        return [];
    }

    protected function hasVirusInContent($filePathAndName): bool
    {
        return (bool)stripos(
            file_get_contents($filePathAndName, false, null, 0, 1024),
            'virus'
        );
    }

    /**
     * Product name of the antivirus scanner for this service
     *
     * @return string The name of the product
     */
    public function getProductName(): string
    {
        return 'TYPO3 Development AV';
    }
}

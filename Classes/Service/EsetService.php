<?php

namespace Extrameile\EmAntivirus\Service;

use TYPO3\CMS\Core\Utility\CommandUtility;

class EsetService extends \TYPO3\CMS\Core\Service\AbstractService implements \Extrameile\EmAntivirus\Service\ScanServiceInterface
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
    public function scanFile($filePathAndName)
    {
        $cmd = CommandUtility::getCommand('esets_scan');

        if ($cmd !== false) {
            exec($cmd . ' --clean-mode=none '  . escapeshellarg($filePathAndName), $output, $return);
            if ($return !== 0) {
                switch ($return) {
                    case 1:
                    case 50:
                        return ['Virus detected'];
                    case 10:
                        return ['Not all files could be scanned so rejected'];
                    case 100:
                        return ['Error while scanning so rejected'];
                    default:
                        return ['Unknown scan result so rejected'];
                }
            }
        } else {
            return ['Antivirus scanner ESET File Security could not be executed.'];
        }

        return [];
    }

    /**
     * Product name of the antivirus scanner for this service
     *
     * @return string The name of the product
     */
    public function getProductName()
    {
        return 'ESET File Security';
    }
}

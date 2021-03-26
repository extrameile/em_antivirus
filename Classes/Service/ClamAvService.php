<?php

namespace Extrameile\EmAntivirus\Service;

use TYPO3\CMS\Core\Utility\CommandUtility;

class ClamAvService extends \TYPO3\CMS\Core\Service\AbstractService implements \Extrameile\EmAntivirus\Service\ScanServiceInterface
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
        $cmd = CommandUtility::getCommand('clamscan');

        if ($cmd !== false) {
            exec($cmd . ' '  . escapeshellarg($filePathAndName), $output, $return);
            if ($return !== 0) {
                switch ($return) {
                    case 1:
                        return ['Virus detected'];
                    case 2:
                        return ['Error while scanning so rejected'];
                    case 137:
                        return ['Out of memory'];
                    default:
                        return ['Unknown scan result so rejected, code ' . $return];
                }
            }
        } else {
            return ['Antivirus scanner ClamAV could not be executed.'];
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
        return 'ClamAV';
    }
}

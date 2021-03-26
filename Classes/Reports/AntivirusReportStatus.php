<?php
namespace Extrameile\EmAntivirus\Reports;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Service\EnableFileService;

/**
 * Performs several checks about the antivirus health
 */
class AntivirusReportStatus implements \TYPO3\CMS\Reports\StatusProviderInterface
{
    private $scannerServices = [];

    /**
     * Run status
     *
     * @return array List of statuses
     */
    public function getStatus()
    {
        $this->loadAllScannerServices();
        $statuses = [
            'isOneInstalled' => $this->isOneInstalled(),
            'isSignatureOutdated' => $this->isSignatureOutdated()
        ];
        return $statuses;
    }

    /**
     * Looks if one of the antivirus services have an antivirus scanner installed
     */
    protected function isOneInstalled()
    {
        // Service will only be returned if scanner executable can be found
        if (count($this->scannerServices)) {
            // It's an object so service could be created
            $value = $GLOBALS['LANG']->getLL('status_ok');
            foreach ($this->scannerServices as $service) {
                $message[] = $service->getProductName();
            }
            $message = implode(', ', $message);
            $severity = \TYPO3\CMS\Reports\Status::OK;
        } else {
            // It's NOT an object so no service available
            $value = $GLOBALS['LANG']->getLL('status_insecure');
            $message = 'No antivirus scanner found';
            $severity = \TYPO3\CMS\Reports\Status::ERROR;
        }

        return GeneralUtility::makeInstance(
            \TYPO3\CMS\Reports\Status::class,
            'Antivirus scanner available',
            $value,
            $message,
            $severity
        );
    }

    /**
     * Looks if one or more antivirus services have an outdated signature file
     */
    protected function isSignatureOutdated()
    {
        while (is_object($service = GeneralUtility::makeInstanceService('antivirusScanner', 'fileScanner', $excludeServiceKeys))) {
            $excludeServiceKeys[] = $service->getServiceKey();
        }

        return GeneralUtility::makeInstance(
            \TYPO3\CMS\Reports\Status::class,
            'Antivirus signature up-to-date',
            'Not available yet',
            '',
            \TYPO3\CMS\Reports\Status::NOTICE
        );
    }

    protected function loadAllScannerServices()
    {
        $excludeServiceKeys = [];

        while (is_object($service = GeneralUtility::makeInstanceService('antivirusScanner', 'fileScanner', $excludeServiceKeys))) {
            $excludeServiceKeys[] = $service->getServiceKey();
            $this->scannerServices[] = $service;
        }
    }
}

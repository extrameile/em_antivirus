<?php
defined('TYPO3_MODE') || die();

/** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
    \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
);

$signalSlotDispatcher->connect(
    \TYPO3\CMS\Core\Resource\ResourceStorage::class,
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PreFileAdd,
    \Extrameile\EmAntivirus\Slot\ResourceStorageSlot::class,
    \Extrameile\EmAntivirus\Slot\ResourceStorageSlot::SLOT_PreFileAdd
);

$signalSlotDispatcher->connect(
    \TYPO3\CMS\Core\Resource\ResourceStorage::class,
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PreFileReplace,
    \Extrameile\EmAntivirus\Slot\ResourceStorageSlot::class,
    \Extrameile\EmAntivirus\Slot\ResourceStorageSlot::SLOT_PreFileReplace
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService(
    'em_antivirus',
    'antivirusScanner',
    \Extrameile\EmAntivirus\Service\ClamAvService::class,
    [
        'title' => 'Clam AV Service',
        'description' => 'Scans files/folders with clamav',
        'subtype' => 'fileScanner,folderScanner',
        'available' => true,
        'priority' => 50,
        'quality' => 50,
        'os' => '',
        'exec' => 'clamscan',
        'className' => \Extrameile\EmAntivirus\Service\ClamAvService::class,
    ]
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService(
    'em_antivirus',
    'antivirusScanner',
    \Extrameile\EmAntivirus\Service\EsetService::class,
    [
        'title' => 'Eset Antivirus Service',
        'description' => 'Scans files/folders with eset antivirus',
        'subtype' => 'fileScanner,folderScanner',
        'available' => true,
        'priority' => 80,
        'quality' => 60,
        'os' => '',
        'exec' => 'esets_scan',
        'className' => \Extrameile\EmAntivirus\Service\EsetService::class
    ]
);

$typo3Version = '0.0.0';

if (!defined('TYPO3_version')) {
    // Since TYPO3 10.3
    $typo3Version = (new \TYPO3\CMS\Core\Information\Typo3Version())
        ->getVersion();
} else {
    $typo3Version = TYPO3_version;
}

if (version_compare($typo3Version, '9.5.0', '>=')) {
    $currentApplicationContext = \TYPO3\CMS\Core\Core\Environment::getContext();
} else {
    $currentApplicationContext = \TYPO3\CMS\Core\Utility\GeneralUtility::getApplicationContext();
}


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService(
    'em_antivirus',
    'antivirusScanner',
    \Extrameile\EmAntivirus\Service\DevelopmentAvService::class,
    [
        'title' => 'TYPO3 Development AV Service',
        'description' => 'Scans files for "virus" as content in the first 1024 bytes',
        'subtype' => 'fileScanner,folderScanner',
        'available' => $currentApplicationContext->isDevelopment(),
        'priority' => 1,
        'quality' => 1,
        'os' => '',
        'className' => \Extrameile\EmAntivirus\Service\DevelopmentAvService::class
    ]
);

// Add to reports Module
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['system'][]
    = \Extrameile\EmAntivirus\Reports\AntivirusReportStatus::class;

unset($signalSlotDispatcher);

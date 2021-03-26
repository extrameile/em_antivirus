<?php

namespace Extrameile\EmAntivirus\Slot;

use TYPO3\CMS\Core\Resource as Resource;

class ResourceStorageSlot
{
    const SLOT_PreFileAdd = 'preFileAdd';
    const SLOT_PreFileReplace = 'preFileReplace';

    /**
     * Slot for the preFileAdd signal
     *
     * @param string $targetFileName
     * @param Resource\Folder $targetFolder
     * @param string $sourceFilePath
     * @param Resource\ResourceStorage $resourceStorage
     * @param Resource\Driver\DriverInterface $driver
     */
    public function preFileAdd(
        $targetFileName,
        Resource\Folder $targetFolder,
        $sourceFilePath,
        Resource\ResourceStorage $resourceStorage,
        Resource\Driver\DriverInterface $driver
    ) {
        \Extrameile\EmAntivirus\Utility\AntivirusUtility::scanFile($sourceFilePath, true);
    }

    /**
     * Slot for the preFileReplace signal
     *
     * @param Resource\FileInterface $file
     * @param string $localFilePath
     */
    public function preFileReplace(
        Resource\FileInterface $file,
        $localFilePath
    ) {
        \Extrameile\EmAntivirus\Utility\AntivirusUtility::scanFile($localFilePath, true);
    }
}

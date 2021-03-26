<?php

namespace Extrameile\EmAntivirus\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class AntivirusUtility
{
    /**
     * Lets scan a local file by all registered AV Services.
     *
     * @param string $filePathAndName File to be scanned.
     * @param boolean $addNotify Set to true if you want flashMessages added.
     */
    public static function scanFile($filePathAndName, $addNotify = false)
    {
        $excludeServiceKeys = [];
        $errors = [];
        $serviceProductName = [];

        while (is_object($serviceObj = GeneralUtility::makeInstanceService('antivirusScanner', 'fileScanner', $excludeServiceKeys))) {
            $excludeServiceKeys[] = $serviceObj->getServiceKey();

            if ($serviceObj instanceof \Extrameile\EmAntivirus\Service\ScanServiceInterface) {
                $errors = array_merge($errors, $serviceObj->scanFile($filePathAndName));

                $serviceProductName[] = $serviceObj->getProductName();
                unset($serviceObj);
            } else {
                self::notify(
                    'The antivirus scanner with serviceKey "' . $serviceObj->getServiceKey() . '" does not work correctly.',
                    \TYPO3\CMS\Core\Messaging\FlashMessage::WARNING
                );
            }
        }

        if ($addNotify) {
            if (count($errors) > 0) {
                self::notify(implode("\n", $errors), \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
            } elseif (count($serviceProductName) === 0) {
                self::notify('No antivirus scanner found.', \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
            } else {
                self::notify('No virus found. Scanned by: ' . "\n" . implode(', ', $serviceProductName));
            }
        }

        if (count($serviceProductName) === 0) {
            throw new \Extrameile\EmAntivirus\Exception\NoScannerFoundException('No antivirus scanner found.');
        }

        if (count($errors) > 0) {
            throw new \Extrameile\EmAntivirus\Exception\VirusDetectedException(implode('<br />', $errors));
        }
    }

    /**
     * Notifies the user using a Flash message.
     *
     * @param string $message The message
     * @param integer $severity Optional severity, must be either of \TYPO3\CMS\Core\Messaging\FlashMessage::INFO,
     *                          \TYPO3\CMS\Core\Messaging\FlashMessage::OK, \TYPO3\CMS\Core\Messaging\FlashMessage::WARNING
     *                          or \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR.
     *                          Default is \TYPO3\CMS\Core\Messaging\FlashMessage::OK.
     * @return void
     * @internal This method is public only to be callable from a callback
     */
    public static function notify($message, $severity = \TYPO3\CMS\Core\Messaging\FlashMessage::OK)
    {
        if (TYPO3_MODE !== 'BE') {
            return;
        }
        $flashMessage = GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Messaging\FlashMessage::class,
            $message,
            '',
            $severity
        );
        /** @var $flashMessageService \TYPO3\CMS\Core\Messaging\FlashMessageService */
        $flashMessageService = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Messaging\FlashMessageService::class);
        /** @var $defaultFlashMessageQueue \TYPO3\CMS\Core\Messaging\FlashMessageQueue */
        $defaultFlashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
        $defaultFlashMessageQueue->enqueue($flashMessage);
    }
}

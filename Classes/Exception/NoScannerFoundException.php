<?php
namespace Extrameile\EmAntivirus\Exception;

/**
 * An exception when a virus was found
 * Not clean but otherwise it will result in an HTTP 500 Error
 * See catch block of TYPO3\CMS\Core\Utility\File\ExtendedFileUtility:func_upload()
 *
 */
class NoScannerFoundException extends \TYPO3\CMS\Core\Resource\Exception\UploadException
{
}

<?php

namespace Extrameile\EmAntivirus\Service;

interface ScanServiceInterface
{
    /**
     * Scans the given file (Service subtype fileScanner)
     *
     * @param string $filePathAndName Complete path and filename of the file
     *
     * @return string[] Array of error messages from scanner
     */
    public function scanFile($filePathAndName);

    /**
     * Product name of the antivirus scanner for this service
     *
     * @return string The name of the product
     */
    public function getProductName();
}

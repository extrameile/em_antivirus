# Extrameile Antivirus

This extension checks all files which are added or replaced in the TYPO3 backend with an Antivirus service like ClamAV
or ESET. Other Antivirus systems can be added by providing them as a scan service which implements the ScanServiceInterface.
In development context, you might not have a compatible scanner installed, so there is a "DevelopmentAvService" which
only scans if an uploaded file includes the word "virus" in its content.

All available scanners will be executed, an exception occurs if no scanner was available and no file will be accepted.
This way you could also add multiple upload filters for content checks.

## Does it work with every upload?

No, this extension connects to the FAL Upload Event and processes the file. The TYPO3 Core backend uses the FAL, but if
you use non FAL file fields in your TCA it will not run. You need to implement a check and call the service yourself. Also
frontend forms are not handled out of the box if they do not use the FAL upload process (EXT:form does, Powermail does
not). If you don't use FAL you need to call the service yourself, e.g. by calling the the provided
utility class.

```
\Extrameile\EmAntivirus\Utility\AntivirusUtility::scanFile($sourceFilePath, true);
```

The first parameter is the complete file path and name of the file to be scanned, the second parameter defines if FlashMessages
should be generated or not. There are 2 possible exceptions that can be thrown.

\Extrameile\EmAntivirus\Exception\NoScannerFoundException If no scanner can be found for execution.
\Extrameile\EmAntivirus\Exception\VirusDetectedException If the scanner found one or more infections in given file.

## HowTo configure ClamAV

On a standard system, with clamav installed at one of the searchable paths no configuration is needed. If the binary is not in
the search path or if open_basedir restrictions are set then the executable can not be found. You need to configure the path in your
LocalConfiguration. The detection in TYPO3 Report Module only works properly, if no open_basedir restrictions are set.

## HowTo configure ESET

Depending on the used version of eset, the executable names will differ. In older versions, it was called "eset_scan",
in newer versions "cls" seems to be used. Both use the same parameters and have the same return codes.
The eset_scan binary is found automagically if in search path and the open_basedir restrictions are not set. Otherwise, you need
to add them in your LocalConfiguration.

## HowTo use TYPO3_CONF_VARS for configuring executables

If TYPO3 can not find your executables, as they aren't in the search path or if open_basedir restrictions are set, you can configure the
correct place of the executables inside your LocalConfiguration (or AdditionalConfiguration).

Please be aware that if you use the binSetup configuration, the TYPO3 reports module will show green "antivirus available" regardless
if the executable exists or not.

The following configuration allows you to add more binary search paths for searching the executables:

### Additional binPath
```
$GLOBALS['TYPO3_CONF_VARS']['SYS']['binPath'] = '/opt/eset/esets/sbin/';
```

### clamscan path
Or you need to provide the complete path and filename of the executable.
Example for ClamAV:
```
$GLOBALS['TYPO3_CONF_VARS']['SYS']['binSetup'] = 'clamscan=/usr/bin/clamscan';
```

### eset_scan path
Example for ESET FileSecurity with eset_scan:
```
$GLOBALS['TYPO3_CONF_VARS']['SYS']['binSetup'] = 'esets_scan=/opt/eset/esets/sbin/esets_scan';
```

### cls path
Example for ESET FileSecurity with cls:
```
$GLOBALS['TYPO3_CONF_VARS']['SYS']['binSetup'] = 'esets_scan=/opt/eset/efs/sbin/cls/cls';
```
## HowTo Test the functionality

After completing the setup, upload an eicar testfile and the scanner should kick in. Be aware that you might have to
disable your local virus scanner to download the file!
The file will be blocked right after the upload is done, an error message will show up and it will not be moved to your
chosen upload folder.

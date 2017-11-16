# libconnect

Libconnect is an TYPO3 extension which was inital developed by Avonis for the Staats- und Universitätsbibliothek Hamburg Carl von Ossietzky (SUB). The SUB develop the continuously.
With libconnect it´s possible to display the information of EZB and DBIS of the University Regensburg on an TYPO3 based website.

Here is the german [Manual](doc/manual.pdf "Ausführliches Manual").

Changes in new Version 5.2.0

    - Compatibility to TYPO3 8.7
    - Depcated function ExtensionManagementUtility::extRelPath changed to ExtensionManagementUtility::extPath
    - Static data in crdate and tstamp changed to UNIX_TIMESTAMP();
    - EZB:
        - new field "Preistyp Anmerkung"
        - Keywords are now links with search for keyword
    - DBIS:
        -  check has library access and list who else 

    Tested with: TYPO3 6.2, 8.7
# libconnect

Libconnect is an TYPO3 extension which was inital developed by Avonis for the Staats- und Universitätsbibliothek Hamburg Carl von Ossietzky (SUB). The SUB develop the continuously.
With libconnect it´s possible to display the information of EZB and DBIS of the University Regensburg on an TYPO3 based website.

Here is the german [Manual](doc/manual.pdf "Ausführliches Manual").

Visit our git repository: https://github.com/subhh/libconnect

## Changes in new Version 5.3.1
    - Security fix: XSS in search results
    - EZB: - fixed search URL
    - ZDB-Titlehistory: Set curl timeout to 2 seconds for more stability

## Changes in new Version 5.3.0

    - Fixed XML-Error handling

    - Tx_Libconnect_Resources_Private_Lib_Httppageconnection: constant port removed, now depends on URL

    - Tx_Libconnect_Resources_Private_Lib_Xmlpageconnection: constant port removed, now depends on URL

    - JQuery is now local and in a new version

    - EZB:

        - title history added

            - It shows the zbd-id, the name and the issue date

            - The values of the displayed journals as a css class named "ezb-detail-tithistory-active".

        - getMoreDetails fixed

    - DBIS: Fixed double double dot in locallang.xml

    Tested with: TYPO3 6.2, 8.7

## Configuration

1. Include static template
2. Set the db IDs. 
    
        plugin.tx_libconnect {
            ezbbibid = SUBHH
            dbisbibid = sub_hh
            zdbsid = vid:dbid
        }


3. Set the plugins in your pages.

## Customize your design

1. Set paths to templates, partials and layouts

### TYPO3 7.6 and newer

        plugin.tx_libconnect {
            view {
                templateRootPaths {
                    0 = EXT:libconnect/Resources/Private/Templates/
                    1 = <yourPath>
                }
                partialRootPaths {
                    0 = EXT:libconnect/Resources/Private/Partials/
                    1 = <yourPath>
                }

                layoutRootPaths {
                    0 = EXT:libconnect/Resources/Private/Layouts/
                    1 = <yourPath>
                }
            }
        }

### TYPO3 6.2

        plugin.tx_libconnect {
            view {
                templateRootPath = EXT:libconnect/Resources/Private/Templates/
                partialRootPath = EXT:libconnect/Resources/Private/Partials/
                layoutRootPath = EXT:libconnect/Resources/Private/Layouts/
            }
        }


2. Deactivate standard CSS

        plugin.tx_libconnect {
            settings {
                ezbNoCSS = 1
                dbisNoCSS = 1
            }
        }

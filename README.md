# libconnect

Libconnect is an TYPO3 extension which was initially developed by Avonis for the Staats- und Universitätsbibliothek Hamburg Carl von Ossietzky (SUB).
The SUB maintains it now.
With libconnect it is possible to display the information of EZB and DBIS of the University Regensburg on an TYPO3 based website.

Here is the German [Manual](doc/manual.pdf "Ausführliches Manual").

Visit our git repository: https://github.com/subhh/libconnect

## 7.0.0
    - DBIS: warpto link changed to https
    - EZB: Title history fixed.
    - Guzzle is now used to do http request.
    - Changed file endings of setup and constant to typoscript for better compatibility to TYPO3 8.7 and newer.
    - Translation changed to XLIFF format.
    - fixed deprecated translation path in flexforms
    - fixed deprecated translation path for hidden in TCA
    - flexforms sections are now translated
    - JavaScript: fixed wrong path to JQuery
    - min Compatibility now 8.7

Tested with 
    - TYPO3 8.7.24
    - TYPO3 9.5.4

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

### Set paths to templates, partials and layouts

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

### Deactivate standard CSS

        page.includeCSS.dbis >
        page.includeCSS.ezb >

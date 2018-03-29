# libconnect

Libconnect is an TYPO3 extension which was inital developed by Avonis for the Staats- und Universitätsbibliothek Hamburg Carl von Ossietzky (SUB). The SUB develop the continuously.
With libconnect it´s possible to display the information of EZB and DBIS of the University Regensburg on an TYPO3 based website.

Here is the german [Manual](doc/manual.pdf "Ausführliches Manual").

## Changes in new Version 5.2.3

    - EZB: detail view: field "Preistyp Anmerkung" fixed for language english

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

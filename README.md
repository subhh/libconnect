# libconnect

Libconnect is an TYPO3 extension which was initially developed by Avonis for the Staats- und Universitätsbibliothek Hamburg Carl von Ossietzky (SUB).
The SUB maintains it now.
With libconnect it is possible to display the information of EZB and DBIS of the University Regensburg on an TYPO3 based website.

Here is the German [Manual](doc/manual.pdf "Ausführliches Manual").

Visit our git repository: https://github.com/subhh/libconnect

## 8.0.2
    - EZB- Details:
        - Title history.
            - ZDBID and period now in same underlined line like original EZB view.
            - Fixed sorting of precursor vom oldes to newest.
            - Fixed incomplete list
        - added open access policy search link to SHERPA/RoMEO
        - fixed keywords
    - Fixed composer.json

Tested with 
    - TYPO3 10.4.14

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

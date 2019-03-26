# libconnect

Libconnect is an TYPO3 extension which was inital developed by Avonis for the Staats- und Universitätsbibliothek Hamburg Carl von Ossietzky (SUB). The SUB develop the continuously.
With libconnect it´s possible to display the information of EZB and DBIS of the University Regensburg on an TYPO3 based website.

Here is the german [Manual](doc/manual.pdf "Ausführliches Manual").

Visit our git repository: https://github.com/subhh/libconnect

## Changes in new Version 6.0.0
    - Compatibility to TYPO3 9
        - Viehelpers with "np" (9+) are for TYPO3 9 and higher
    - Fixed some errors for TYPO3 8.4+
    - Fixed suppressed CSS file.
    - Not used User function "user_libconnect_hasSelectedPluginForCSSInclude" deleted.
    - Fixes in locallang
    - CSS: height of form elements are now the same
    - EZB:
        - Reworked building of access legend and links in result lists.
        - Performance optimizatoin.
        - fixed paging in "New in EZB"
        - fixed legend in "New in EZB"
        - changed legend to form like list and search of "New in EZB"
        - new in ezb always displayed
    - DBIS:
        - Fixed Flexform for sorting of list
        - Miniform:
            - sends subject on changed access
            - option without a constraint added
            - access change is fixed and works
            - you can now filter search results by access type
        - Search: default values of colors and ocolors removed
        - Fixed missing access information on top plugin template
        - Extended search: reset button added
        - Reworked code
        - Performance optimizatoin.
        - Detail:
            - fixed &auml; in access type
    - Language:
        - deprecated: tx_libconnect.dbis.search.licence

Tested with 
    - TYPO3 7.6.32
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

        plugin.tx_libconnect {
            settings {
                ezbNoCSS = 1
                dbisNoCSS = 1
            }
        }

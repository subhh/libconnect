# libconnect

Libconnect is an TYPO3 extension which was initially developed by Avonis for the Staats- und Universitätsbibliothek Hamburg Carl von Ossietzky (SUB).
The SUB maintains it now.
With libconnect it is possible to display the information of EZB and DBIS of the University Regensburg on an TYPO3 based website.

Here is the German [Manual](doc/manual.pdf "Ausführliches Manual").

Visit our git repository: https://github.com/subhh/libconnect

## 10.0.0
    - Compatibility to TYPO3 12.4
    - EZB:
        - Details: 
            - Fixed problems with subjects
            - Categorys added.
        - Search:
            - removed last "AND" or "OR" in the text, if it is the last or only element
        - List: Fixed lc tag.

Tested with 
    - TYPO3 11.15.12

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


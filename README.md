# libconnect

Libconnect is an TYPO3 extension which was initially developed by Avonis for the Staats- und Universitätsbibliothek Hamburg Carl von Ossietzky (SUB).
The SUB maintains it now.
With libconnect it is possible to display the information of EZB and DBIS of the University Regensburg on an TYPO3 based website.

Here is the German [Manual](doc/manual.pdf "Ausführliches Manual").

Visit our git repository: https://github.com/subhh/libconnect

## 7.0.4
    - Capture GuzzleHttp\Exception\ServerException for more stability
    - DBIS:
        - New in DBIS: fixed entries of collections, there were listed new entries of all subjects
        - Changed licence legend text to default texts of dbis
        - Changed all rzblx10.uni-regensburg.de links to https
        - removed not used translations
    - EZB: 
        - stabilit fix, if zdb service not working
        - Changed link to zdb from http://www.zdb-opac.de to https://zdb-katalog.de
        - Changed some more warpto links and a link to rzbl1x1.uni-regensburg.de to https.
    - ZDB: Changed urls for full format request to https

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

.. include:: ../Includes.txt


.. _configuration:

=============
Configuration
=============

.. contents::
   :local:
   :depth: 2



Target group: **Developers, Integrators**


Set zdb configuration
=====================

You see the default values.

code-block:: typoscript

    plugin.tx_libconnect {
        zdbsid = vid:dbid
        zdbbibid = 
        zdbsigel = 
        zdbisil = 
        zdbbik = 
    }


Set paths to your own templates, partials and layouts
=====================================================

code-block:: typoscript

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


Deactivate standard CSS
=======================

If you don´t want to use the original css, deactivate it.

code-block:: typoscript
    page.includeCSS.dbis >
    page.includeCSS.ezb >


EZB: Overwrite access information texts
=======================================

If the text of ezb is empty, it will be overwritten by configuration. If force = yes, all texts are overwritten.

code-block:: typoscript
    plugin.tx_libconnect {
        settings {
            ezblongaccessinfos{
                force = yes
                de{
                    1 = frei zugänglich
                    2 = Im Campus-Netz sowie für Angehörige der Universität auch extern zugänglich.
                    4 = Für Ihren Standort nicht freigeschaltet. Zum Teil bekommen Sie Zugriff auf Abstracts.
                    6 = Nur für einen Teil der erschienenen Jahrgänge zugänglich.
                }
                en{
                    1 = free available
                    2 = On campus network and member of the university external accessible
                    4 = Free for your location. Partial only access to abstracts.
                    6 = Only a part of the volumes is accessible.
                }
            }

            ezbshortaccessinfos{
                de{
                    1 = frei zugänglich
                    2 = im Campus-Netz zugänglich
                    4 = nicht frei zugänglich
                    6 = nur ein Teil zugänglich
                }
                en{
                    1 = free available
                    2 = only on the Campus-Netz accessible
                    4 = not free accessible
                    6 = only a part is accessible
                }
            }
        }
    }


DBIS: Remove licences from extended search
==========================================
If you want to remove a licence option in the extended search of dbis, use this option.

Default: empty

code-block:: typoscript
    plugin.tx_libconnect {
        settings.dbislicenceforbid.3 = false
        settings.dbislicenceforbid.4 = false
    }


ZDB: filter location information
================================

Comma seperated list of integer. The values are location states which are dispayed.

2 = available, 3 = limited availability (moving wall, etc.), 4 = journal not available

code-block:: typoscript
    plugin.tx_libconnect {
        settings.validStatesList = 1,2
    }


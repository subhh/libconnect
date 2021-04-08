.. include:: ../Includes.txt


.. _admin-manual:

============
Installation
============

.. contents::
   :local:
   :depth: 1



The installation and initial configuration of the extension is as following:

#. Install the extension with the extension manager

#. Include the static TypoScript configuration “libconnect (libconnect)” in your TypoScript template

#. Now you can change in the Constant Editor the values for “ezbid” and “dbisaid” The default values are from the Staats- und Universitätsbibliothek Hamburg. Or you can do it as typoscript in the setup, like this:


    .. code-block:: typoscript
        :linenos:

        plugin.tx_libconnect {
            ezbbibid = yourId
          dbisbibid = yourId
        }
 

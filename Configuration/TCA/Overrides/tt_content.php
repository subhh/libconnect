<?php

/* DBIS */

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Sub.libconnect',
    'Dbis',
    'libconnect: dbis',
    'EXT:libconnect/Resources/Public/Icons/Wizard.gif'
);

// Add flexform for DBIS
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['libconnect_dbis'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('libconnect_dbis', 'FILE:EXT:libconnect/Configuration/FlexForms/dbis_flexform.xml');

/* EZB */

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Sub.libconnect',
    'Ezb',
    'libconnect: ezb',
    'EXT:libconnect/Resources/Public/Icons/Wizard.gif'
);

// Add flexform for EZB
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['libconnect_ezb'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('libconnect_ezb', 'FILE:EXT:libconnect/Configuration/FlexForms/ezb_flexform.xml');

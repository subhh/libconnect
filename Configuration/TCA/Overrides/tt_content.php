<?php

/* DBIS */

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Sub.libconect',
    'Dbis',
    'libconnect: dbis'
);

// Add flexform for DBIS
$TCA['tt_content']['types']['list']['subtypes_addlist']['libconnect_dbis'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('libconnect_dbis', 'FILE:EXT:libconnect/Configuration/FlexForms/dbis_flexform.xml');

/* EZB */

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Sub.libconnect',
    'Ezb',
    'libconnect: ezb'
);

// Add flexform for EZB
$TCA['tt_content']['types']['list']['subtypes_addlist']['libconnect_ezb'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('libconnect_ezb', 'FILE:EXT:libconnect/Configuration/FlexForms/ezb_flexform.xml');

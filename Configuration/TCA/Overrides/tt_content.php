<?php

/* DBIS */

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Sub.'.$_EXTKEY,
    'Dbis',
    'libconnect: dbis'
);

// Add flexform for DBIS
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_dbis'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($_EXTKEY . '_dbis', 'FILE:EXT:'.$_EXTKEY.'/Configuration/FlexForms/dbis_flexform.xml');

/* EZB */

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Sub.'.$_EXTKEY,
    'Ezb',
    'libconnect: ezb'
);

// Add flexform for EZB
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_ezb'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($_EXTKEY . '_ezb', 'FILE:EXT:'.$_EXTKEY.'/Configuration/FlexForms/ezb_flexform.xml');

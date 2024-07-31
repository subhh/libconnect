<?php

/* DBIS */

$pluginSignature_dbis = \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Libconnect',
    'Dbis',
    'libconnect: dbis',
    'EXT:libconnect/Resources/Public/Icons/Wizard.gif'
);

// Add flexform for DBIS
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature_dbis] = 'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature_dbis, 'FILE:EXT:libconnect/Configuration/FlexForms/dbis_flexform.xml');

/** EZB **/
/* EZB List */
$pluginSignature_ezb_list = \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Libconnect',
    'EzbList',
    'libconnect: EZB List',
    'EXT:libconnect/Resources/Public/Icons/Wizard.gif'
);

// Add flexform
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature_ezb_list] = 'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature_ezb_list, 'FILE:EXT:libconnect/Configuration/FlexForms/ezb_list_flexform.xml');

/* EZB Details */
$pluginSignature_ezb_detail = \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Libconnect',
    'EzbDetail',
    'libconnect: EZB Details',
    'EXT:libconnect/Resources/Public/Icons/Wizard.gif'
);

// Add flexform for EZB
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature_ezb_detail] = 'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature_ezb_detail, 'FILE:EXT:libconnect/Configuration/FlexForms/ezb_details_flexform.xml');
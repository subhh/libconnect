<?php

/** DBIS **/
/* DBIS List */
$pluginSignature_dbis_list = \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Libconnect',
    'DbisList',
    'libconnect: DBIS List',
    'EXT:libconnect/Resources/Public/Icons/Wizard.gif'
);

// Add flexform for DBIS
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature_dbis_list] = 'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature_dbis_list, 'FILE:EXT:libconnect/Configuration/FlexForms/dbis_list_flexform.xml');

/* DBIS Detail */
$pluginSignature_dbis_detail = \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Libconnect',
    'DbisDetail',
    'libconnect: DBIS Detail',
    'EXT:libconnect/Resources/Public/Icons/Wizard.gif'
);

// Add flexform for DBIS
//$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature_dbis_detail] = 'pi_flexform';

//\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature_dbis_detail, 'FILE:EXT:libconnect/Configuration/FlexForms/dbis_detail_flexform.xml');

/* DBIS Search */
$pluginSignature_dbis_search = \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Libconnect',
    'DbisSearch',
    'libconnect: DBIS Advanced Search',
    'EXT:libconnect/Resources/Public/Icons/Wizard.gif'
);

// Add flexform for DBIS
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature_dbis_search] = 'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature_dbis_search, 'FILE:EXT:libconnect/Configuration/FlexForms/dbis_search_flexform.xml');

/* DBIS Sidebar */
$pluginSignature_dbis_sidebar = \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Libconnect',
    'DbisSidebar',
    'libconnect: DBIS Sidebar',
    'EXT:libconnect/Resources/Public/Icons/Wizard.gif'
);

// Add flexform for DBIS
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature_dbis_sidebar] = 'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature_dbis_sidebar, 'FILE:EXT:libconnect/Configuration/FlexForms/dbis_search_flexform.xml');

/* DBIS New */
$pluginSignature_dbis_new = \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Libconnect',
    'DbisNew',
    'libconnect: DBIS New Databases',
    'EXT:libconnect/Resources/Public/Icons/Wizard.gif'
);

// Add flexform for DBIS
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature_dbis_new] = 'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature_dbis_new, 'FILE:EXT:libconnect/Configuration/FlexForms/dbis_new_flexform.xml');

/* DBIS Top */
$pluginSignature_dbis_top = \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Libconnect',
    'DbisTop',
    'libconnect: DBIS Top Databases',
    'EXT:libconnect/Resources/Public/Icons/Wizard.gif'
);

// Add flexform for DBIS
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature_dbis_top] = 'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature_dbis_top, 'FILE:EXT:libconnect/Configuration/FlexForms/dbis_top_flexform.xml');


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


/* EZB Search */
$pluginSignature_ezb_search = \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Libconnect',
    'EzbSearch',
    'libconnect: EZB Extended search',
    'EXT:libconnect/Resources/Public/Icons/Wizard.gif'
);

// Add flexform
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature_ezb_search] = 'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature_ezb_search, 'FILE:EXT:libconnect/Configuration/FlexForms/ezb_search_flexform.xml');

/* EZB Sidebar */
$pluginSignature_ezb_miniform = \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Libconnect',
    'EzbSidebar',
    'libconnect: EZB Sidebar',
    'EXT:libconnect/Resources/Public/Icons/Wizard.gif'
);

// Add flexform
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature_ezb_miniform] = 'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature_ezb_miniform, 'FILE:EXT:libconnect/Configuration/FlexForms/ezb_sidebar_flexform.xml');

/* EZB Contact */
$pluginSignature_ezb_contact = \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Libconnect',
    'EzbContact',
    'libconnect: EZB Contact',
    'EXT:libconnect/Resources/Public/Icons/Wizard.gif'
);

// Add flexform
//$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature_ezb_contact] = 'pi_flexform';

//\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature_ezb_contact, 'FILE:EXT:libconnect/Configuration/FlexForms/ezb_contact_flexform.xml');

/* EZB New */
$pluginSignature_ezb_new = \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Libconnect',
    'EzbNew',
    'libconnect: EZB New Journals',
    'EXT:libconnect/Resources/Public/Icons/Wizard.gif'
);

// Add flexform
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature_ezb_new] = 'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature_ezb_new, 'FILE:EXT:libconnect/Configuration/FlexForms/ezb_new_flexform.xml');

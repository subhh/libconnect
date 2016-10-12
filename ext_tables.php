<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Sub.'.$_EXTKEY,
    'Dbis',
    'libconnect: dbis'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Sub.'.$_EXTKEY,
    'Ezb',
    'libconnect: ezb'
);

// Add flexform for DBIS
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_dbis'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($_EXTKEY . '_dbis', 'FILE:EXT:'.$_EXTKEY.'/Configuration/FlexForms/dbis_flexform.xml');

// Add flexform for EZB
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_ezb'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($_EXTKEY . '_ezb', 'FILE:EXT:'.$_EXTKEY.'/Configuration/FlexForms/ezb_flexform.xml');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript/', 'libconnect');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_libconnect_domain_model_subject', 'EXT:libconnect/Resources/Private/Language/locallang_csh_tx_libconnect_domain_model_subject.xml');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_libconnect_domain_model_subject');
$TCA['tx_libconnect_domain_model_subject'] = array(
    'ctrl' => array(
        'title'    => 'Fachgebiet',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => 'ORDER BY title',
        //'dividers2tabs' => TRUE,
        //'versioningWS' => 2,
        //'versioning_followPages' => TRUE,
        //'origUid' => 't3_origuid',
        //'languageField' => 'sys_language_uid',
        //'transOrigPointerField' => 'l10n_parent',
        //'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden',
            //'starttime' => 'starttime',
            //'endtime' => 'endtime',
        ),
        'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Subject.php',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_libconnect_domain_model_subject.gif'
    )
);


if (TYPO3_MODE == 'BE') {
    $TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_libconnect_dbis_wizicon'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/Wizicon/class.tx_libconnect_dbis_wizicon.php';
    $TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_libconnect_ezb_wizicon'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/Wizicon/class.tx_libconnect_ezb_wizicon.php';
}
?>
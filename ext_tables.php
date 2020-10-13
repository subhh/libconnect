<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Add language
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_libconnect_domain_model_subject', 'EXT:libconnect/Resources/Private/Language/locallang_csh_tx_libconnect_domain_model_subject.xml');

// Allow to store subject table on standard pages
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_libconnect_domain_model_subject');

if (TYPO3_MODE == 'BE') {
    $TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_libconnect_dbis_wizicon'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('libconnect') . 'Configuration/Wizicon/class.tx_libconnect_dbis_wizicon.php';
    $TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_libconnect_ezb_wizicon'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('libconnect') . 'Configuration/Wizicon/class.tx_libconnect_ezb_wizicon.php';
}

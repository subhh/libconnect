<?php

defined('TYPO3') or die('Access denied.');

// Add language
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_libconnect_domain_model_subject', 'EXT:libconnect/Resources/Private/Language/locallang_csh_tx_libconnect_domain_model_subject.xml');

// Allow to store subject table on standard pages
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_libconnect_domain_model_subject');

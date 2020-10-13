<?php

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Sub.' . $_EXTKEY,
    'Dbis',
    [
        'Dbis' => 'displayForm'
    ],
    // non-cacheable actions
    [
        'Dbis' => 'displayDetail, displayList, displayMiniForm, displayNew'
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Sub.' . $_EXTKEY,
    'Ezb',
    [
        'Ezb' => 'displayForm, displayContact'
    ],
    // non-cacheable actions
    [
        'Ezb' => 'displayDetail, displayList, displayMiniForm, displayNew, displayParticipantsForm'
    ]
);

call_user_func(function() {
    try {
        $enableDebugLog = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get('libconnect', 'debug');
        if ($enableDebugLog) {
            $GLOBALS['TYPO3_CONF_VARS']['Sub']['Libconnect']['writerConfiguration'] = [
                \TYPO3\CMS\Core\Log\LogLevel::DEBUG => [
                    \TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
                        'logFileInfix' => 'libconnect',
                    ]
                ]
            ];
        }
    } catch (\Throwable $exception) {}
});
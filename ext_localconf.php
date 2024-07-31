<?php

defined('TYPO3') or die('Access denied.');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Libconnect',
    'Dbis',
    [
        \Subhh\Libconnect\Controller\DbisController::class => 'displayList, displayDetail, displayList, displayMiniForm, displayNew'
    ],
    // non-cacheable actions
    [
        \Subhh\Libconnect\Controller\DbisController::class => 'displayForm, displayDetail, displayList, displayMiniForm, displayNew'
    ]
);

//\Subhh\Libconnect\Controller\EzbController::class => 'displayList, displayDetail, displayForm, displayMiniForm, displayContact, displayNew, displayParticipantsForm'

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Libconnect',
    'EzbList',
    [
        \Subhh\Libconnect\Controller\EzbController::class => 'displayList'
    ],
    // non-cacheable actions
    [
        \Subhh\Libconnect\Controller\EzbController::class => 'displayList'
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Libconnect',
    'EzbDetail',
    [
        \Subhh\Libconnect\Controller\EzbController::class => 'displayDetail'
    ],
    // non-cacheable actions
    [
        \Subhh\Libconnect\Controller\EzbController::class => 'displayDetail'
    ]
);

try {
    $enableDebugLog = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get('libconnect', 'debug');
    if ($enableDebugLog) {
        $GLOBALS['TYPO3_CONF_VARS']['Subhh']['Libconnect']['writerConfiguration'] = [
            \TYPO3\CMS\Core\Log\LogLevel::DEBUG => [
                \TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
                    'logFileInfix' => 'libconnect',
                ]
            ]
        ];
    }
} catch (\Throwable $exception) {}

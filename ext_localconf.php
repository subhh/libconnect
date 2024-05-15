<?php

defined('TYPO3') or die('Access denied.');

call_user_func(function() {

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Libconnect',
        'Dbis',
        [
            \Sub\Libconnect\Controller\DbisController::class => 'displayForm'
        ],
        // non-cacheable actions
        [
            \Sub\Libconnect\Controller\DbisController::class => 'displayDetail, displayList, displayMiniForm, displayNew'
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Libconnect',
        'Ezb',
        [
            \Sub\Libconnect\Controller\EzbController::class => 'displayForm, displayContact'
        ],
        // non-cacheable actions
        [
            \Sub\Libconnect\Controller\EzbController::class => 'displayDetail, displayList, displayMiniForm, displayNew, displayParticipantsForm'
        ]
    );

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

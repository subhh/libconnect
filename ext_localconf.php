<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Libconnect',
    'Dbis',
    [
        \Sub\Libconnect\Controller\DbisController::class => 'displayForm, displayDetail'
    ],
    // non-cacheable actions
    [
        \Sub\Libconnect\Controller\DbisController::class => 'displayList, displayMiniForm, displayNew'
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Libconnect',
    'Ezb',
    [
        \Sub\Libconnect\Controller\EzbController::class => 'displayForm, displayContact, displayDetail'
    ],
    // non-cacheable actions
    [
        \Sub\Libconnect\Controller\EzbController::class => 'displayList, displayMiniForm, displayNew, displayParticipantsForm'
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

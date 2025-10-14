<?php

defined('TYPO3') or die('Access denied.');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Libconnect',
    'DbisList',
    [
        \Subhh\Libconnect\Controller\DbisController::class => 'displayList'
    ],
    // non-cacheable actions
    [
        \Subhh\Libconnect\Controller\DbisController::class => 'displayList'
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Libconnect',
    'DbisDetail',
    [
        \Subhh\Libconnect\Controller\DbisController::class => 'displayDetail'
    ],
    // non-cacheable actions
    [
        \Subhh\Libconnect\Controller\DbisController::class => 'displayDetail'
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Libconnect',
    'DbisSearch',
    [
        \Subhh\Libconnect\Controller\DbisController::class => 'displayForm'
    ],
    // non-cacheable actions
    [
        \Subhh\Libconnect\Controller\DbisController::class => 'displayForm'
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Libconnect',
    'DbisSidebar',
    [
        \Subhh\Libconnect\Controller\DbisController::class => 'displayMiniForm'
    ],
    // non-cacheable actions
    [
        \Subhh\Libconnect\Controller\DbisController::class => 'displayMiniForm'
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Libconnect',
    'DbisNew',
    [
        \Subhh\Libconnect\Controller\DbisController::class => 'displayNew'
    ],
    // non-cacheable actions
    [
        \Subhh\Libconnect\Controller\DbisController::class => 'displayNew'
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Libconnect',
    'DbisTop',
    [
        \Subhh\Libconnect\Controller\DbisController::class => 'displayTop'
    ],
    // non-cacheable actions
    [
        \Subhh\Libconnect\Controller\DbisController::class => 'displayTop'
    ]
);

/** EZB **/
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

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Libconnect',
    'EzbSearch',
    [
        \Subhh\Libconnect\Controller\EzbController::class => 'displayForm'
    ],
    // non-cacheable actions
    [
        \Subhh\Libconnect\Controller\EzbController::class => 'displayForm'
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Libconnect',
    'EzbSidebar',
    [
        \Subhh\Libconnect\Controller\EzbController::class => 'displayMiniForm'
    ],
    // non-cacheable actions
    [
        \Subhh\Libconnect\Controller\EzbController::class => 'displayMiniForm'
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Libconnect',
    'EzbContact',
    [
        \Subhh\Libconnect\Controller\EzbController::class => 'displayContact'
    ],
    // non-cacheable actions
    [
        \Subhh\Libconnect\Controller\EzbController::class => 'displayContact'
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Libconnect',
    'EzbNew',
    [
        \Subhh\Libconnect\Controller\EzbController::class => 'displayNew'
    ],
    // non-cacheable actions
    [
        \Subhh\Libconnect\Controller\EzbController::class => 'displayNew'
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Libconnect',
    'EzbParticipantsForm',
    [
        \Subhh\Libconnect\Controller\EzbController::class => 'displayParticipantsForm'
    ],
    // non-cacheable actions
    [
        \Subhh\Libconnect\Controller\EzbController::class => 'displayParticipantsForm'
    ]
);

//Fix problem form returns 404 &cHash empty
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = '^tx_libconnect';
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = '^libconnect';

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

<?php

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

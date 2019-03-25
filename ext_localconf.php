<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Sub.'.$_EXTKEY,
    'Dbis',
    array(
        'Dbis' => 'displayDetail, displayList, displayMiniForm, displayForm, displayNew'
    ),
    // non-cacheable actions
    array(
        'Dbis' => ''
    )
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Sub.'.$_EXTKEY,
    'Ezb',
    array(
        'Ezb' => 'displayDetail, displayList, displayMiniForm, displayForm, displayNew, displayParticipantsForm, displayContact'
    ),
    // non-cacheable actions
    array(
        'Ezb' => ''
    )
);
?>

<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Sub.'.$_EXTKEY,
    'Dbis',
    array(
        'Dbis' => 'displayForm'
    ),
    // non-cacheable actions
    array(
        'Dbis' => 'displayDetail, displayList, displayMiniForm, displayNew'
    )
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Sub.'.$_EXTKEY,
    'Ezb',
    array(
        'Ezb' => 'displayForm, displayContact'
    ),
    // non-cacheable actions
    array(
        'Ezb' => 'displayDetail, displayList, displayMiniForm, displayNew, displayParticipantsForm'
    )
);
?>

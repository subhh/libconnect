<?php
if (!defined ('TYPO3_MODE')) {
    die ('Access denied.');
}

$TCA['tx_libconnect_domain_model_subject'] = array(
    'ctrl' => $TCA['tx_libconnect_domain_model_subject']['ctrl'],
    'interface' => array(
        //'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, dbisid, ezbnotation',
        'showRecordFieldList' => 'hidden,title,dbisid,ezbnotation',
    ),
    'types' => array(
        //'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, title, dbisid, ezbnotation,--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'),
        //'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, title, dbisid, ezbnotation,--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'),
        '0' => array('showitem' => 'hidden;;1;;1-1-1, title, dbisid, ezbnotation')
    ),
    'palettes' => array(
        '1' => array('showitem' => '')
    ),
    'columns' => array(
        /*'sys_language_uid' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
            'config' => array(
                'type' => 'select',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => array(
                    array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
                    array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
                ),
            ),
        ),*/
        /*'l10n_parent' => array(
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('', 0),
                ),
                'foreign_table' => 'tx_libconnect_domain_model_subject',
                'foreign_table_where' => 'AND tx_libconnect_domain_model_subject.pid=###CURRENT_PID### AND tx_libconnect_domain_model_subject.sys_language_uid IN (-1,0)',
            ),
        ),
        'l10n_diffsource' => array(
            'config' => array(
                'type' => 'passthrough',
            ),
        ),
        't3ver_label' => array(
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.versionLabel',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            )
        ),*/
        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config' => array(
                'type' => 'check',
            )
        ),
        'title' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:libconnect/Resources/Private/Language/locallang_db.xml:tx_libconnect_domain_model_subject.title',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            )
        ),
        'dbisid' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:libconnect/Resources/Private/Language/locallang_db.xml:tx_libconnect_domain_model_subject.dbisid',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            )
        ),
        'ezbnotation' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:libconnect/Resources/Private/Language/locallang_db.xml:tx_libconnect_domain_model_subject.ezbnotation',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            )
        )
    )
);
?>
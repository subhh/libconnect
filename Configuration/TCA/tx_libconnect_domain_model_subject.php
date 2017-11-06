<?php
if (!defined ('TYPO3_MODE')) {
    die ('Access denied.');
}

return array(
    'ctrl' => array(
        'title'    => 'Fachgebiet',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => 'title',
        //'dividers2tabs' => TRUE,
        //'versioningWS' => 2,
        //'versioning_followPages' => TRUE,
        //'origUid' => 't3_origuid',
        //'languageField' => 'sys_language_uid',
        //'transOrigPointerField' => 'l10n_parent',
        //'transOrigDiffSourceField' => 'l10n_diffsource',
//'type' => 'type',
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden',
            //'starttime' => 'starttime',
            //'endtime' => 'endtime',

        ),
        'iconfile' => 'EXT:libconnect/Resources/Public/Icons/tx_libconnect_domain_model_subject.gif'
    ),
    'interface' => array(
        'showRecordFieldList' => 'hidden,title,dbisid,ezbnotation',
    ),
    'types' => array(
        '0' => array('showitem' => 'hidden, title, dbisid, ezbnotation')
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
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config' => array(
                'type' => 'check',
            ),
	    'exclude' => 1
        ),
        'title' => array(
            'label' => 'LLL:EXT:libconnect/Resources/Private/Language/locallang_db.xml:tx_libconnect_domain_model_subject.title',
            'config' => array(
                'type' => 'input',
                'size' => 30,
		'max' => 255,
                'eval' => 'trim,required'
            ),
           'exclude' => 0
        ),
        'dbisid' => array(
            'label' => 'LLL:EXT:libconnect/Resources/Private/Language/locallang_db.xml:tx_libconnect_domain_model_subject.dbisid',
            'config' => array(
                'type' => 'input',
                'size' => 30,
		'max' => 255,
                'eval' => 'trim'
            ),
            'exclude' => 0
        ),
        'ezbnotation' => array(
            'label' => 'LLL:EXT:libconnect/Resources/Private/Language/locallang_db.xml:tx_libconnect_domain_model_subject.ezbnotation',
            'config' => array(
                'type' => 'input',
                'size' => 30,
		'max' => 255,
                'eval' => 'trim'
            ),
            'exclude' => 0
        )
    )
);
?>

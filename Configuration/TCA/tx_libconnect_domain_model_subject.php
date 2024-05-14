<?php

return [
    'ctrl' => [
        'title'    => 'Fachgebiet',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => 'title',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'iconfile' => 'EXT:libconnect/Resources/Public/Icons/tx_libconnect_domain_model_subject.gif'
    ],
    'interface' => [
    ],
    'types' => [
        '0' => ['showitem' => 'hidden, title, dbisid, ezbnotation']
    ],
    'palettes' => [
        '1' => ['showitem' => '']
    ],
    'columns' => [
        'hidden' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
            'exclude' => 1
        ],
        'title' => [
            'label' => 'LLL:EXT:libconnect/Resources/Private/Language/locallang_db.xlf:tx_libconnect_domain_model_subject.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'eval' => 'trim,required'
            ],
           'exclude' => 0
        ],
        'dbisid' => [
            'label' => 'LLL:EXT:libconnect/Resources/Private/Language/locallang_db.xlf:tx_libconnect_domain_model_subject.dbisid',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'eval' => 'trim'
            ],
            'exclude' => 0
        ],
        'ezbnotation' => [
            'label' => 'LLL:EXT:libconnect/Resources/Private/Language/locallang_db.xlf:tx_libconnect_domain_model_subject.ezbnotation',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'eval' => 'trim'
            ],
            'exclude' => 0
        ]
    ]
];

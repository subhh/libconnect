<?php

$EM_CONF['libconnect'] = [
    'title' => 'libconnect',
    'description' => 'Diese Extension ist von Avonis im Auftrag der Staats- und Universitaetsbibliothek Hamburg entwickelt worden. Mit ihr lassen sich Ergebnisse aus den Informationssystemen EZB und DBIS der Universitaet Regensburg direkt in das TYPO3-System einbinden.',
    'category' => 'plugin',
    'author' => 'Avonis New Media / SUB Hamburg',
    'author_email' => 'torsten.witt@sub.uni-hamburg.de',
    'author_company' => '',
    'module' => '',
    'state' => 'stable',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '9.0.2',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99',
            'extbase' => '',
            'fluid' => '',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'Sub\\Libconnect\\' => 'Classes',
        ],
    ],
];

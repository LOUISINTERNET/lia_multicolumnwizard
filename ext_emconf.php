<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'LIA MultiColumnWizard',
    'description' => '',
    'category' => 'plugin',
    'author' => 'LOUIS TYPO3 Developers',
    'author_email' => 'info@dev.louis.info',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-12.5.99',
            'lia_ctypes_container' => '',
            'lia_ctypes' => '',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];

<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'LIA MultiColumnWizard',
    'description' => 'This extension adds an new TCA-Type that allows to group fields into rows that can be multiplied.',
    'category' => 'plugin',
    'author' => 'LOUIS TYPO3 Developers',
    'author_email' => 'info@dev.louis.info',
    'state' => 'stable',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-12.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];

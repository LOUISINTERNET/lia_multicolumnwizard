<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'LIA MultiColumnWizard',
    'description' => 'This extension adds an new TCA-Type that allows to group fields into rows that can be multiplied.',
    'category' => 'plugin',
    'author' => 'LOUIS TYPO3 Developers',
    'author_company' => 'LOUIS INTERNET',
    'author_email' => 'devs@louis.info',
    'state' => 'stable',
    'version' => '2.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-14.4.99',
        ],
    ],
];

<?php

defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

call_user_func(function () {
    ExtensionManagementUtility::addTCAcolumns('tt_content', [
        'tx_lia_multicolumnwizard' => [
            'exclude' => true,
            'label' => 'Elements',
            'config' => [
                'type' => 'user',
                'renderType' => 'multiColumnWizard',
                'columnFields' => [],
            ],
        ],
    ]);

    ExtensionManagementUtility::addTCAcolumns('tx_liactypes_domain_model_ctypes', [
        'tx_lia_multicolumnwizard' => [
            'exclude' => true,
            'label' => 'Elements',
            'config' => [
                'type' => 'user',
                'renderType' => 'multiColumnWizard',
                'columnFields' => [],
            ],
        ],
    ]);
});

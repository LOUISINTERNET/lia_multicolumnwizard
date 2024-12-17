<?php

defined('TYPO3') or die();

call_user_func(
    function () {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1637607885] = [
            'nodeName' => 'multiColumnWizard',
            'priority' => 40,
            'class' => \LIA\LiaMulticolumnwizard\Form\Element\MultiColumnWizard::class,
        ];

        $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['mcw'][] = 'LIA\\LiaMulticolumnwizard\\ViewHelpers';
    }
);

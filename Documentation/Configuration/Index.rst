.. _Configuration:

=============
Configuration
=============

CType TCA Configuration
============================
Here you can see a configuration with all the options for configuring the columns of the multicolumn wizard

Implementation on parent elements
------------------------------------
you can use the option 'showLabelAboveField' => TRUE, if this is set the label will be shown in all rows above the field

.. code-block:: php
    :caption: TCA field Configuration

    'fieldname' => [
        'exclude' => 1,
        'label' => 'TestField',
        'config' => [
            'type' => 'user',
            'renderType' => 'multiColumnWizard',
            'columnFields' => [
                'fieldName_1' => [
                    'divClass' => 'col-sm-2',
                    'type' => 'text',
                    'label' => 'Textxfield'
                ],
                'fieldName_2' => [
                    'divClass' => 'col-sm-2',
                    'type' => 'textarea',
                    'label' => 'Textarea',
                    'cols' => 40,
                    'rows' => 5
                ],
                'fieldName_3' => [
                    'divClass' => 'col-sm-2',
                    'type' => 'checkbox',
                    'label' => 'Checkbox'
                ],
                'fieldName_4' => [
                    'divClass' => 'col-sm-2',
                    'type' => 'select',
                    'label' => 'Select',
                    'options' => [
                        'value' => 'name',
                        'value2' => 'name2',
                        'value3' => 'name3',
                        'value4' => 'name4',
                        'value5' => 'name5'
                    ]
                ],
                'fieldName_5' => [
                    'divClass' => 'col-sm-2',
                    'label' => 'Icon',
                    'type' => 'select',
                    'options' => '',
                    'optionsFunction' => ['\\LIA\\LiaMulticolumnwizard\\Utilities\\IconFactory','getIcons',['EXT:my_extkey/Resources/Public/path/to/my/Icons.json']],
                ],
                'fieldName_6' => [
                    'divClass' => 'col-sm-2',
                    'label' => 'Reference',
                    'type' => 'select',
                    'options' => '',
                    'optionsFunction' => ['\\LIA\\LiaMulticolumnwizard\\Utilities\\ReferenceFactory','getReference',['pages', 'uid', 'title']],
                ],
                'fieldName_7' => [
                    'divClass' => 'col-sm-2',
                    'label' => 'Languages',
                    'type' => 'select',
                    'options' => '',
                    'optionsFunction' => ['\\LIA\\LiaMulticolumnwizard\\Utilities\\TCAFactory','getAvailableLanguagesForAllSites',[]],
                ],
                'fieldName_8' => [
                    'divClass' => 'col-sm-2',
                    'type' => 'link',
                    'label' => 'Link',
                    'linkUseField' => 'fieldname_hidden_link_field',
                ],
            ]
        ]
    ],
    'fieldname_hidden_link_field' = [
        'exclude' => 1,
        'label' => 'Hidden link field for the link wizard of fieldName_8',
        'config' => [
            'type' => 'link',
        ]
    ];

IconFactory
~~~~~~~~~~~
This factory creates an icon selection from the icons in the JSON file.
Your Icon.json file schould look like this.

.. code-block:: json
    :caption: Icons.json example

    [
        {"name":"arrow-down","file":"arrow-down.svg"},
        {"name":"book","file":"book.svg"},
        {"name":"car","file":"car.svg"},
        {"name":"chevron-down","file":"chevron-down.svg"},
        {"name":"chevron-right","file":"chevron-right.svg"},
        {"name":"cloud-download","file":"cloud-download.svg"},
        {"name":"comment-alt-exclamation","file":"comment-alt-exclamation.svg"},
        {"name":"comment-alt","file":"comment-alt.svg"},
        {"name":"compass","file":"compass.svg"},
        {"name":"dollar-sign","file":"dollar-sign.svg"},
        {"name":"envelope","file":"envelope.svg"},
        {"name":"euro","file":"euro.svg"},
        {"name":"external-link","file":"external-link.svg"},
        {"name":"fax","file":"fax.svg"},
        {"name":"hands-helping","file":"hands-helping.svg"},
        {"name":"leaf","file":"leaf.svg"},
        {"name":"map-marker-alt-solid","file":"map-marker-alt-solid.svg"},
        {"name":"map-marker-alt","file":"map-marker-alt.svg"},
        {"name":"paper-plane","file":"paper-plane.svg"},
        {"name":"phone","file":"phone.svg"},
        {"name":"swatchbook","file":"swatchbook.svg"},
        {"name":"tasks","file":"tasks.svg"},
        {"name":"user-headset","file":"user-headset.svg"},
        {"name":"users","file":"users.svg"},
        {"name":"video","file":"video.svg"},
        {"name":"wrench","file":"wrench.svg"}
    ]

Link Field
~~~~~~~~~~

In order to use the link feature you need to also create a field of type link in the current TCA and reference it in the multicolumnwizard link field in the linkUseField attribute. This field will be automatically hidden by Javascript. In lia_ctypes you could use a liaoptional field for example:

.. code-block:: php
    :caption: Example

    $ctype['columnsOverrides']['liaoptional5']['config']['columnFields'] = [
        'fieldName_8' => [
            'divClass' => 'col-sm-2',
            'type' => 'link',
            'label' => 'Link',
            'linkUseField' => 'liaoptional6',
        ],
    ];

    // the hidden link field for fieldName_8
    $ctype['columnsOverrides']['liaoptional6'] = [
        'exclude' => 1,
        'label' => 'Link',
        'config' => [
            'type' => 'link',
        ]
    ];

Implementation on child elements
--------------------------------

.. code-block:: php
    :caption:

    $tca_columns['tx_lia_multicolumnwizard']['config']['columnFields'] = [
        'fieldName_1' => [
            'divClass' => 'col-sm-2',
            'type' => 'text',
            'label' => 'Textxfield'
        ],
        'fieldName_2' => [
            'divClass' => 'col-sm-2',
            'type' => 'textarea',
            'label' => 'Textarea',
            'cols' => 40,
            'rows' => 5
        ],
        'fieldName_3' => [
            'divClass' => 'col-sm-2',
            'type' => 'checkbox',
            'label' => 'Checkbox'
        ],
        'fieldName_4' => [
            'divClass' => 'col-sm-2',
            'type' => 'select',
            'label' => 'Select',
            'options' => [
                'value' => 'name',
                'value2' => 'name2',
                'value3' => 'name3',
                'value4' => 'name4',
                'value5' => 'name5'
            ]
        ],
        'fieldName_5' => [
            'divClass' => 'col-sm-2',
            'label' => 'Icon',
            'type' => 'select',
            'options' => '',
            'optionsFunction' => ['\\LIA\\LiaMulticolumnwizard\\Utilities\\IconFactory','getIcons',['EXT:lia_package/Resources/Public/Icons.json']],
        ],
        'fieldName_6' => [
            'divClass' => 'col-sm-2',
            'label' => 'Reference',
            'type' => 'select',
            'options' => '',
            'optionsFunction' => ['\\LIA\\LiaMulticolumnwizard\\Utilities\\ReferenceFactory','getReference',['pages', 'uid', 'title']],
        ],
        'fieldName_7' => [
            'divClass' => 'col-sm-2',
            'label' => 'Languages',
            'type' => 'select',
            'options' => '',
            'optionsFunction' => ['\\LIA\\LiaMulticolumnwizard\\Utilities\\TCAFactory','getAvailableLanguagesForAllSites',[]],
        ],
        'fieldName_8' => [
            'divClass' => 'col-sm-2',
            'type' => 'link',
            'label' => 'Link',
            'linkUseField' => 'fieldname_hidden_link_field',
        ],
    ];

    $tca_columns['fieldname_hidden_link_field'] = [
        'exclude' => 1,
        'label' => 'Hidden link field for the link wizard of fieldName_8',
        'config' => [
            'type' => 'link',
        ]
    ];

    $overrideChildTca['types'][1]['showitem'] = '... ,tx_lia_multicolumnwizard, fieldname_hidden_link_field, ...';

Override Backend-Tempalte Paths
-------------------------------

All Backendtemplates can be overriden using the 'general backend template override feature':
https://docs.typo3.org/m/typo3/reference-tsconfig/12.4/en-us/PageTsconfig/Templates.html

Excample
~~~~~~~~

To overwrite the partial for text-input, place a Partials into the directory:
    `extensions/my_extension_key/Resources/Private/Backend/Partials/Fields/text.html`

and define this in your TSConfig:
    :typoscript:`templates.typo3_ext/lia_multicolumnwizard.1721919321 = my_vendor/my_extension_key:Resources/Private/Extensions/LiaMulticolumnWizard/Backend`


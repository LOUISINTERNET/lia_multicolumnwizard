.. _Configuration:

=============
Configuration
=============

CType TCA Configuration
============================
Here you can see a configuration with all the options for configuring the columns of the multicolumn wizard

Implementation on parent elements
------------------------------------

.. code-block:: php
    :caption: TCA field configuration

    'multicolumnwizard' => [
        'exclude' => 1,
        'label' => 'MulticolumnField',
        'config' => [
            'type' => 'user',
            'renderType' => 'multiColumnWizard',
            'columnFields' => [
                'exampleMultiColumnWizardTextInput' => [
                    'divClass' => 'col-sm-2',
                    'type' => 'text',
                    'label' => 'Textfield'
                    'showLabelAboveField' => true,
                ],
                'exampleMultiColumnWizardTextareaInput' => [
                    'divClass' => 'col-sm-2',
                    'type' => 'textarea',
                    'label' => 'Textarea',
                    'cols' => 40,
                    'rows' => 5
                    'showLabelAboveField' => true,
                ],
                'exampleMultiColumnWizardCheckbox' => [
                    'divClass' => 'col-sm-2',
                    'type' => 'checkbox',
                    'label' => 'Checkbox'
                ],
                'exampleMultiColumnWizardSelect' => [
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
                'exampleMultiColumnWizardSelectIcon' => [
                    'divClass' => 'col-sm-2',
                    'label' => 'Icon',
                    'type' => 'select',
                    'options' => '',
                    'optionsFunction' => [LIA\LiaMulticolumnwizard\Utilities\IconFactory::class . '->getIcons', ['iconPath' => 'EXT:your_extension/Resources/Public/Icons.json']],
                ],
                'exampleMultiColumnWizardReference' => [
                    'divClass' => 'col-sm-2',
                    'label' => 'Reference',
                    'type' => 'select',
                    'options' => '',
                    'optionsFunction' => [LIA\LiaMulticolumnwizard\Utilities\ReferenceFactory::class . '->getReference', ['tableName' => 'pages', 'valueField' => 'uid', 'outputNameField' => 'title']],
                ],
                'exampleMultiColumnWizardLanguage' => [
                    'divClass' => 'col-sm-2',
                    'label' => 'Languages',
                    'type' => 'select',
                    'options' => '',
                    'optionsFunction' => [LIA\LiaMulticolumnwizard\Utilities\TCAFactory::class . '->getAvailableLanguagesForAllSites'],
                ],
                'exampleMultiColumnWizardLinkInput' => [
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
        'label' => 'Hidden link field used by the link wizard of exampleMultiColumnWizardLinkInput',
        'config' => [
            'type' => 'link',
        ]
    ];

Limiting the number of rows
---------------------------

The field level options :php:`minitems` and :php:`maxitems` limit how many rows the
wizard accepts. They are named and behave like their counterparts on the native TCA
types :php:`inline`, :php:`select` and :php:`group`. Both default to :php:`0`, which
means "no limit".

.. code-block:: php
    :caption: TCA field configuration

    'multicolumnwizard' => [
        'exclude' => 1,
        'label' => 'Buttons',
        'config' => [
            'type' => 'user',
            'renderType' => 'multiColumnWizard',
            'minitems' => 0,
            'maxitems' => 4,
            'columnFields' => [
                // ...
            ],
        ],
    ],

*  :php:`maxitems` counts **rows**. Once it is reached, the "Create new record after
   this record" button of every row is disabled. Records that already exceed the limit
   are still rendered - they are flagged instead of silently truncated, so no data is
   lost when a limit is lowered later on.
*  :php:`minitems` counts **filled rows**. The wizard always keeps one row on screen,
   so a single untouched row counts as zero items.

Violations mark the field with the FormEngine error class. This blocks saving and
raises the standard "fields missing" modal, the same way native TCA types do. Like
those types, the limits are not enforced by the DataHandler.

..  warning::
    :php:`minitems` must not exceed :php:`maxitems`, and both must be non-negative
    integers. Anything else throws an
    :php:`LIA\LiaMulticolumnwizard\Exceptions\Backend\InvalidOptionValueException`
    while rendering the form.

Field length options
--------------------

Columns of type :php:`text` and :php:`textarea` accept :php:`max` and :php:`min` -
named after the native TCA options for :php:`input` and :php:`text` - to limit the
number of characters.

.. code-block:: php
    :caption: Column length options

    'exampleMultiColumnWizardTextInput' => [
        'divClass' => 'col-sm-2',
        'type' => 'text',
        'label' => 'Textfield',
        'max' => 40,
        'min' => 3,
    ],

*  :php:`max` renders as :html:`maxlength`, so the browser stops further input.
   :php:`text` columns default to :php:`255`; :php:`textarea` columns stay uncapped
   unless :php:`max` is set.
*  :php:`min` renders as :html:`minlength` and is additionally checked in JavaScript,
   because FormEngine submits the form itself and the browser therefore never runs its
   own :html:`minlength` validation. Empty fields are never flagged - use
   :php:`minitems` to require rows.

Set up the DatabaseFileds in your database tables:

.. code-block:: sql
    :caption: EXT:/your_extension/ext_tables.sql

    CREATE TABLE tx_yourextension_domain_model_item (
        multicolumnwizard mediumtext,
    );

and expand your model with variable, getter and setters:

.. code-block:: php
    :caption: EXT:/your_extension/Domain/Model/Itenm.php

    /**
    * multicolumnwizard
    *
    * @var string
    */
    protected string $multicolumnwizard = '';

    [...]

    /**
    * Returns the multicolumnwizard list (as json)
    *
    * @return string
    */
    public function getMulticolumnwizard(): string
    {
        return $this->multicolumnwizard;
    }

    /**
    * Sets the multicolumnwizard
    *
    * @param string $multicolumnwizard
    * @return void
    */
    public function setMulticolumnwizard(string $multicolumnwizard): void
    {
        $this->multicolumnwizard = $multicolumnwizard;
    }





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

In order to use the link feature you need to also create a field of type link in the current TCA and reference it in the multicolumnwizard link 
field in the linkUseField attribute. This field will be automatically hidden by Javascript. 

.. code-block:: php
    :caption: Link Field Example
    :emphasize-lines: 8,13,21

    $ctype = &$GLOBALS['TCA']['tt_content']['types']['lia_ctypes_mcw_test'];

    $ctype['columnsOverrides']['multicolumnwizard']['config']['columnFields'] = [
        'exampleMultiColumnWizardLinkInput' => [
            'divClass' => 'col-sm-2',
            'type' => 'link',
            'label' => 'Link',
            'linkUseField' => 'fieldname_hidden_link_field',
        ],
    ];

    // the hidden link field for exampleMultiColumnWizardLinkInput
    $ctype['columnsOverrides']['fieldname_hidden_link_field'] = [
        'exclude' => 1,
        'label' => 'Link',
        'config' => [
            'type' => 'link',
        ]
    ];

    $ctype['types'][1]['showitem'] = '... , tx_lia_multicolumnwizard, fieldname_hidden_link_field, ...';


..  warning::
    The `fieldname_hidden_link_field` Field has to be added to showItems to be rendered in the Form, otherwise the linkwizard will not be shown.

Implementation on child elements
--------------------------------

you can use the option `'showLabelAboveField' => true`, if this is set the label will be shown in every row

.. code-block:: php
    :caption: Useage of showLabelAboveField

    $tca_columns['tx_lia_multicolumnwizard']['config']['columnFields'] = [
        'fieldName_1' => [
            'divClass' => 'col-sm-2',
            'type' => 'text',
            'label' => 'Textxfield'
            'showLabelAboveField' => true,
        ],
        'fieldName_2' => [
            'divClass' => 'col-sm-2',
            'type' => 'textarea',
            'label' => 'Textarea',
            'showLabelAboveField' => true,
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
            'optionsFunction' => [LIA\LiaMulticolumnwizard\Utilities\IconFactory::class . '->getIcons', ['iconPath' => 'EXT:your_extension/Resources/Public/Icons.json']],
        ],
        'fieldName_6' => [
            'divClass' => 'col-sm-2',
            'label' => 'Reference',
            'type' => 'select',
            'options' => '',
            'optionsFunction' => [LIA\LiaMulticolumnwizard\Utilities\ReferenceFactory::class . '->getReference', ['tableName' => 'pages', 'valueField' => 'uid', 'outputNameField' => 'title']],
        ],
        'fieldName_7' => [
            'divClass' => 'col-sm-2',
            'label' => 'Languages',
            'type' => 'select',
            'options' => '',
            'optionsFunction' => [LIA\LiaMulticolumnwizard\Utilities\TCAFactory::class . '->getAvailableLanguagesForAllSites'],
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



Override Backend-Template Paths
-------------------------------

Backendtemplates can be overriden using the `general backend template override feature<https://docs.typo3.org/m/typo3/reference-tsconfig/12.4/en-us/PageTsconfig/Templates.html>`__


Example
~~~~~~~~

To overwrite the partial for text-input, place a Partials into the directory:

`extensions/your_extension/Resources/Private/Backend/Partials/Fields/text.html`

and define this in your TSConfig:

.. code-block:: typoscript
    templates.lia/lia_multicolumnwizard.1721919321 = your_vendor/your_extension:Resources/Private/Extensions/LiaMulticolumnWizard/Backend



Options function
----------------

Here you can also use your own function to generate the selection options. The function must return an array, otherwise you will receive an exception.
Enter the complete class name as the first parameter. The second parameter is the function name followed by an array with all the parameters required to execute your function.

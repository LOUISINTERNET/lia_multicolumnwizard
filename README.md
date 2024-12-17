# LIA Multi column wizard

[[_TOC_]]

## Global Information
The DB field should be mediumtext (ext_tables.sql)

```
fieldname mediumtext,
```

## FE
```
<f:if condition="!{mcw:isEmpty(json: data.tx_lia_multicolumnwizard)}">
  <f:for each="{mcw:getMultiColumnWizardValues(json:data.fieldname)}" as="row">
      {row.fieldName_1}
      {row.fieldName_2}
      {row.fieldName_3}
  </f:for>
</f:if>
```

## ViewHelper

### GetMultiColumnWizardValuesViewHelper
This ViewHelper convert the given json string into an array

Argument | Datatype | Description | Required | Default
---------|----------|-------------|----------|--------
json | String | The json string of the multicolumn field. | fasle | null
associative | Boolean | If set to true you will get an associative array otherwise a stdclass object. | false | false

### GetSelectValuesViewHelper
This ViewHelper call a function with given parameter.

Argument | Datatype | Description | Required | Default
---------|----------|-------------|----------|--------
configuration | String | Json | false | null
optionsFunction | String | Json | false | null

### IsEmptyViewHelper
This ViewHelper check if the given json string contains only empty entries and returns a true if every item is empty and false if just one of the items is not empty.

Argument | Datatype | Description | Required | Default
---------|----------|-------------|----------|--------
json | String | The json string of the multicolumn field. | true |

## BE Examples
### CType TCA Configuration

#### Implementation on parent elements

```
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    'tx_lia_multicolumnwizard, fieldname_hidden_link_field',
    'lia_ctypes_multicolumnwizard',
    'after: bodytext'
);

$ctype['columnsOverrides']['tx_lia_multicolumnwizard']['config']['columnFields'] = [
    'fieldName_1' => [
        'divClass' => 'col-sm-2',
        'type' => 'text',
        'label' => 'Textfield',
    ],
    'fieldName_2' => [
        'divClass' => 'col-sm-2',
        'type' => 'textarea',
        'label' => 'Textarea',
        'cols' => 40,
        'rows' => 5,
    ],
    'fieldName_3' => [
        'divClass' => 'col-sm-2',
        'type' => 'checkbox',
        'label' => 'Checkbox',
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
            'value5' => 'name5',
        ],
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

$ctype['columnsOverrides']['fieldname_hidden_link_field'] = [
    'exclude' => 1,
    'label' => 'Hidden link field for the link wizard of fieldName_8',
    'config' => [
        'type' => 'link',
    ]
];
```
you can use the option 'showLabelAboveField' => TRUE, if this is set the label will be shown in all rows above the field

#### Implementation on child elements
```
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
```
### General TCA Configuration

```
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
```

#### Link Field

In order to use the link feature you need to also create a field of type link in the current TCA and reference it in the multicolumnwizard link field in the linkUseField attribute. This field will be automatically hidden by Javascript. In lia_ctypes you could use a liaoptional field for example:

```
// your multicolumnwizard field
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
```

## Override Backend-Tempalte Paths
All Backendtemplates can be overriden using the 'general backend template override feature':
https://docs.typo3.org/m/typo3/reference-tsconfig/12.4/en-us/PageTsconfig/Templates.html

Example:
To overwrite the partial for text-input, place a Partials into the directory:
`extensions/lia_multicolumnwizard/Resources/Private/Backend/Partials/Fields/text.html`

and define this in your TSConfig:
```
templates.typo3_ext/lia_multicolumnwizard.1721919321 = lia/lia_package:Resources/Private/Extensions/LiaMulticolumnWizard/Backend
```

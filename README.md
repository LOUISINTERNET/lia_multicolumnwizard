# TYPO3 extension `LIA Multi column wizard`

This extension adds an new TCA-Type that allows to group fields into rows that can be multiplied.

<br>

| | URL |
|---|---
| **Repository:**  | https://github.com/LOUISINTERNET/lia_multicolumnwizard         
| **Issues:**      | https://github.com/LOUISINTERNET/lia_multicolumnwizard/issues  
| **Read online:** | https://docs.typo3.org/p/lia/lia_multicolumnwizard/main/en-us/
| **TER:**         | https://extensions.typo3.org/extension/lia_multicolumnwizard/  

## Compatibility

| Version | TYPO3        |
|---------|--------------|
| 3.x     | 13.4, 14     |
| 2.1–2.3 | 13.4, 14     |
| 2.0     | 13.4         |

## Upgrading from 2.x to 3.0

> **Breaking change:** the column option `linkUseField` was removed. A link column
> that still sets it throws an `InvalidOptionValueException` (code `1793952001`) and
> the record can no longer be edited until the option is removed.

Up to 2.x a column of type `link` opened the link browser through a second TCA field
of type `link` in the same record (the "proxy field" named by `linkUseField`), which
had to be in `showitem` and was hidden by JavaScript. From 3.0 the wizard opens the
link browser itself. The proxy field is not needed anymore and has to go.

The rows stored by the wizard keep their JSON format: **no data migration is
needed**, existing links stay as they are.

### Steps

1. **Raise the constraint** in the `composer.json` of the project:

   ```bash
   composer require "lia/lia_multicolumnwizard:^3.0"
   ```

2. **Find every link column with a proxy field:**

   ```bash
   grep -rn "linkUseField" extensions/ packages/ config/
   ```

   This covers TCA in PHP (`columnFields`, `columnsOverrides`) as well as FlexForm
   XML (`<linkUseField>`).

3. **Remove the `linkUseField` option** from each hit.

   TCA (PHP), before:

   ```php
   'link' => [
       'type' => 'link',
       'label' => 'Link',
       'linkUseField' => 'tx_myext_link_proxy',
   ],
   ```

   after:

   ```php
   'link' => [
       'type' => 'link',
       'label' => 'Link',
   ],
   ```

   FlexForm (XML): delete the `<linkUseField>…</linkUseField>` line of the column.

4. **Carry over restrictions of the proxy field.** If the TCA of the proxy field
   restricted the link browser, set the same values on the wizard column. The
   options sit directly on the column (not under `appearance`):

   | Proxy field (TCA type `link`)            | Wizard column (3.0)      |
   |------------------------------------------|--------------------------|
   | `allowedTypes`                           | `allowedTypes`           |
   | `appearance.allowedOptions`              | `allowedOptions`         |
   | `appearance.allowedFileExtensions`       | `allowedFileExtensions`  |

   Each is a non-empty list of strings. `record` is not expanded to the record link
   handlers; list their identifiers from `TCEMAIN.linkHandler` instead.

5. **Remove the proxy field**, if it existed only for the wizard:
   - its entry in `showitem` or the palette it was placed in,
   - its column definition in the TCA (`addTCAcolumns` or `['columns'][…]`),
   - its column in `ext_tables.sql` and its label in the language files,
   - any code that only existed to position or hide it (event listeners, TSconfig).

   **Check before deleting:** some projects pointed `linkUseField` at a real field
   such as `header_link`. Keep such a field and only remove the option from the
   wizard column.

6. **Flush caches and drop the proxy column** in Admin Tools → Maintenance → Analyze
   Database Structure (`typo3 extension:setup` only adds columns, it does not drop
   them). The column holds no data of its own, at most a copy of a link that is
   stored in the wizard rows anyway.

7. **Check the backend:** open a record with the wizard, pick, change and clear a link
   and save. The former proxy field must no longer appear in the form.

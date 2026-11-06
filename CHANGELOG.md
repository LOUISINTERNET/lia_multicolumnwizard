# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/).

## [3.0.0] - 2026-11-06

### Breaking changes
- The column option `linkUseField` was removed. A link column that still sets it throws an `InvalidOptionValueException` (code `1793952001`), and records with such a wizard can no longer be edited until the option is gone. The wizard opens the link browser itself now, so the proxy field of type `link` that `linkUseField` named is not needed anymore
- The stored rows keep their JSON format; no data migration is needed

#### Upgrade from 2.x
1. Raise the constraint: `composer require "lia/lia_multicolumnwizard:^3.0"`
2. Find all proxy fields: `grep -rn "linkUseField" extensions/ packages/ config/` (TCA in PHP and `<linkUseField>` in FlexForm XML)
3. Remove the `linkUseField` option from every link column
4. Move restrictions of the proxy field's TCA to the wizard column: `allowedTypes` → `allowedTypes`, `appearance.allowedOptions` → `allowedOptions`, `appearance.allowedFileExtensions` → `allowedFileExtensions`
5. Remove the proxy field if it existed only for the wizard: `showitem` / palette entry, TCA column, `ext_tables.sql` column, label, and any code that only positioned or hid it. Keep real fields such as `header_link` that served as proxy
6. Flush caches and drop the proxy column in Analyze Database Structure
7. Open a record with the wizard, pick, change and clear a link, save

See the README for before/after examples.

### Changed
- Link columns open the core link browser themselves. The wizard renders the link browser URL per link column and receives the chosen link in a hidden, never submitted input of its own: TYPO3 v13 writes into it, v14 dispatches its set-link event on the modal, which the wizard forwards to the same input. Link columns therefore work in records, IRRE children and FlexForms
- Link columns accept the column options `allowedTypes`, `allowedOptions` and `allowedFileExtensions`, passed to the link browser like the native TCA type `link` does; `record` is not expanded to the record link handlers

### Fixed
- Text and textarea columns wrote their value into the stored JSON only on blur. Saving with the cursor still in the field (for example with Enter) fires no blur, so the last typed value was lost and an empty column was stored. The JSON is now updated on every input
- With `showLabelAboveField`, the row buttons (add, delete, move) lined up with the column labels instead of the inputs, and consecutive rows had no spacing, so a label touched the input of the row above. The buttons are bottom-aligned now and rows are spaced by `0.75rem`
- A wizard in a FlexForm showed the record column (`[pi_flexform]`) next to its label instead of its own FlexForm field name (for example `[settings.links]`). Only the label was wrong; the value was always stored under the FlexForm field

### Removed
- Column option `linkUseField` and the proxy field it named (see Breaking changes)

## [2.3.0] - 2026-09-04

### Added
- Soft reference parser `multiColumnWizardTypolink` makes the links of a wizard field visible to `sys_refindex`. A wizard stores its rows as JSON, which the core `typolink` parser cannot read, so links maintained in the wizard were not indexed at all: TYPO3 did not warn that a page was still referenced when it was deleted, and the info module did not list the reference
- The parser walks the rows and delegates every value of a `link` column to the core typolink parser, so all link types it supports are covered, page and record links included. Link columns are read from `columnFields` of the field, unioned with the `columnsOverrides` of every record type, because the reference index works on the raw record and does not resolve the type
- Enable it per field with `'softref' => 'multiColumnWizardTypolink'` and run `typo3 referenceindex:update` to index existing records

## [2.2.0] - 2026-09-04

### Added
- Field level options `minitems` and `maxitems` limit the number of rows, named and behaving like their counterparts on the native TCA types `inline`, `select` and `group`
- `maxitems` disables the "create new row" button of every row once the limit is reached; rows beyond an afterwards lowered limit are flagged instead of truncated
- `minitems` counts filled rows only, so the always present empty row counts as zero items
- Column options `max` and `min` limit the number of characters of `text` and `textarea` columns, named after the native TCA options for `input` and `text`
- Limit violations mark the field with the FormEngine error class, which blocks saving and raises the standard "fields missing" modal

### Changed
- The hardcoded 255 character limit of `text` columns became the default of the new `max` option; `textarea` columns stay uncapped unless `max` is set
- `Default.html` renders the column configuration from the new `columnFields` view variable instead of reading it from `data.parameterArray.fieldConf.config`

### Fixed
- The link column resolved its `linkUseField` proxy with a document-wide selector, so in an IRRE collection every child record wrote into and read from the first child's proxy field. The lookup is now scoped to the `.form-irre-object` the wizard belongs to
- Wizards inside an IRRE child record added after page load stayed uninitialized; the mutation observer now also watches the `<objectId>_records` containers
- The TCA `description` of a multiColumnWizard field was never rendered, because the element built its html from the Fluid view alone and skipped `renderFieldInformation()`

## [2.1.0] - 2026-04-01

### Changed
- Add TYPO3 v14 support (v13+v14 dual compatibility)
- Inject LinkService via constructor DI instead of GeneralUtility::makeInstance (Rector)
- Fix missing return type on GetSelectValuesViewHelper::initializeArguments()
- Remove unreachable dead code in GetSelectValuesViewHelper
- Add rector.php and fractor.php as upgrade deliverables
- Bump version to 2.1.0

### Fixed
- Fix JS crash when linkUseField proxy element is missing from DOM
- Fix optionsFunction argument type from string to array
- Fix render() return type safety when configuration is not an array

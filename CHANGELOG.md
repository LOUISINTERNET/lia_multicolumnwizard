# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/).

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

# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/).

## [2.1.0] - 2026-04-01

### Changed
- Add TYPO3 v14 support (v13+v14 dual compatibility)
- Inject LinkService via constructor DI instead of GeneralUtility::makeInstance (Rector)
- Fix missing return type on GetSelectValuesViewHelper::initializeArguments()
- Remove unreachable dead code in GetSelectValuesViewHelper
- Add rector.php and fractor.php as upgrade deliverables
- Bump version to 2.1.0

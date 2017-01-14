# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [UNRELEASED] - 0000-00-00

### Changed

### Fixed

### Added

## [0.2.0-beta] - 2017-01-14

This is the first release to be feature-complete with the legacy MMP code.
All patterns are ported and it does everything MMP does (and more).

### Changed
- Consistent use of naturalWaist as measurement name (no longer waistCircumference)
- AaronAshirt pattern uses new dimension system
- BruceBoxers pattern uses new dimension system
- TamikoTop pattern uses new dimension system
- TrayvonTie pattern uses new dimension system
- HugoHoodie pattern uses new dimension system
- CathrinCorset pattern uses new dimension system
- CathrinCorset was restrucured
- Updated to translations
- Tweaks to the JoostBodyBlock pattern
- Tweaks in themes

### Fixed
- Parts with no paths to render broke the paperless theme
- #15
- #22
- Numerous bug fixes

### Added
- hasPathToRender() method in Part class
- getStartPoint() and getEndPoint() in Path class
- Dimensions
- French translations
- German translations
- WahidWaistcoat Pattern
- TheodoreTrousers Pattern
- TheoTousers Pattern
- Small part titles

## [0.1.0-alpha] - 2016-12-13
### Changed
- Created the (now default) develop branch
- Added this CHANGELOG.md file
- Moved todo to GitHub issues
- Install instructions in README.md
- New compare service + theme
- Style code and type hinting

### Fixed
- Bug in the docs channel not taking measurements into account

### Added
- The WahidWaistcoat pattern
- Buttonhole and Button defs in default theme

## [0.0.4-alpha] - 2016-11-01
### Changed
- Too much to list. This is from the dark early days

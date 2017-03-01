# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [UNRELEASED] - 0000-00-00

### Changed

- Documentation is now [in its own repository](https://github.com/joostdecock/freesewing-docs) and [hosted by GitHub](https://joostdecock.github.io/freesewing-docs/)
- Removed the _BezierToolbox::bezierCoeffs_ method, as it wasn't used
- Removed the _BezierToolbox::cubicRoots_ method, as it wasn't used
- Removed the _BezierToolbox::sgn_ method, as it wasn't used
- Removed the _BezierToolbox::sortSpecial_ method, as it wasn't used
- Removed the _Model::getMeasurements_ methods, as it wasn't used
- Removed the _Part::addSplitCurve_ method, use _Part::splitCurve_ instead
- Removed the _Part::curveEdgeLeft_ method, use _Part::curveEdge_ instead
- Removed the _Part::curveEdgeRight_ method, use _Part::curveEdge_ instead
- Removed the _Part::curveEdgeTop_ method, use _Part::curveEdge_ instead
- Removed the _Part::curveEdgeBottom_ method, use _Part::curveEdge_ instead
- Removed the _Part::findLineLineIntersection_ method, use _Part::beamsCross_ instead
- Removed _Pattern::getSamplerModelFile_ 
- Removed _Pattern::getSamplerModels_
- Removed _themes/Sampler::themeResponse_, parent will do
- Removed _themes/Info::getThemeName_
- Removed replace code from _SvgBlock::add_
- Removed shoulderLength measurement. Use shoulderToShoulder instead
- Removed the _Sampler::getPattern_ method, as it wasn't used
- Removed unused methods from _Transform_
- Removed the _options_ property from _Channel_
- Made _Sampler::addSampledPartsToPattern_ protected
- Made _Sampler::sampleParts_ protected
- Made _Theme::getConfigFile_ protected
- Made _Theme::getTemplateDir_ protected
- Made _Theme::loadTemplates_ protected
- Made _Pattern::getTranslationsDir_ private
- Made _Pattern::getConfigFile_ private
- Made _Pattern::setWidth_ private
- Made _Pattern::setHeight_ private
- Made _Pattern::pileParts_ private
- Made _Pattern::addPartBoundaries_ private
- Made _Theme::loadTemplateHierarchy_ private
- Made _Theme::getClassChain_ private
- Renamed _Pattern::addPart_ to _Pattern::newPart_ to be consisten with method naming in _Part_
- Renamed _Utils::findLineLineIntersection_ to _Utils::lineLineIntersection_
- Renamed _AbstractService_ class to _Service_ to be consistent with other abstract class naming
- Moved _bezierPoint_ method from _Utils_ to _BezierToolbox_
- _PRECISION_ is now a constant in _Point_

### Fixed

- Fixed issue where _Part::newWidthDimensionSm_ did not pass leader attributes

### Added

- Class documentation
- Credits to Polynomial class
- The Sven Sweater pattern
- The _embedFluid_ option to themes
- The _Info_ theme now output a _Access-Control-Allow-Origin_ header
- The _isValidResponse_ in _Channel_, checked by serviced before sending response

## [0.3.0-beta] - 2017-01-26

This is the first release to ship with unit tests.

### Changed

- _channels\Channel\isValidRequest_ is now an abstract method
- Type hinting for _channels\Channel\isValidRequest_
- Renamed the default _themes\Svg_ theme to _themes\Basic_
- Refactored  _Part::offsetTolerance_
- Refactored _Part::findStackIntersections_
- Refactored _Part::fillPathStackGaps_
- Made the neck opening wider in _patterns\JoostBodyBlock_
- Renamed _patterns\JoostBodyBlock_ into _patterns\BrianBodyBlock_

### Fixed

- Issue in _themes\Designer::debugPoints_ where points weren't shown
- Bug in _Part::shiftTowards_

### Added

- Unit tests
- Unit test fixtures
- Added _Polynomial::linearRoots_
- Added _Polynomial::quarticRoots_
- _patterns\TestPattern_ to be used by unit tests
- _patterns\PatternTemplate_ to facilitate new pattern design
- Added _Utils::slug_
- Request options _parts_, _forceParts_, _onlyPoints_, and _markPoints_ and the logic tied to them
- _patterns\TrentTrouserBlock_
- _patterns\SethSelvedgeTrouserBlock_
- _patterns\LesleyLeggings_


## [0.2.0-beta] - 2017-01-14

This is the first release to be feature-complete with the legacy MMP code.
All patterns are ported and it does everything MMP does (and more).

### Changed
- Consistent use of _naturalWaist_ as measurement name (no longer waistCircumference)
- _patterns\AaronAshirt_ uses new dimension system
- _patterns\BruceBoxers_ uses new dimension system
- _patterns\TamikoTop_ uses new dimension system
- _patterns\TrayvonTie_ uses new dimension system
- _patterns\HugoHoodie_ uses new dimension system
- _patterns\CathrinCorset_ uses new dimension system
- _patterns\CathrinCorset_ was restrucured
- Updated to translations
- Tweaks to the _JoostBodyBlock_ pattern
- Tweaks in themes

### Fixed
- Parts with no paths to render broke the paperless theme
- #15
- #22
- Numerous bug fixes

### Added
- _hasPathToRender_ method in _Part_ class
- _getStartPoint_ and _getEndPoint_ in _Path_ class
- Dimensions
- French translations
- German translations
- _WahidWaistcoat_ Pattern
- _TheodoreTrousers_ Pattern
- _TheoTousers_ Pattern
- Small part titles

## [0.1.0-alpha] - 2016-12-13
### Changed
- Created the (now default) develop branch
- Added this _CHANGELOG.md_ file
- Moved todo to GitHub issues
- Install instructions in _README.md_
- New compare service + theme
- Style code and type hinting

### Fixed
- Bug in the _docs_ channel not taking measurements into account

### Added
- The _WahidWaistcoat_ pattern
- Buttonhole and Button defs in default theme

# Change Log

> All notable changes to freesewing core should be be documented in this file.
> Freesewing uses [Semantic Versioning](http://semver.org/).

## Version 1.1.0 - UNRELEASED

**Release date**: UNRELEASED

This is a non-trivial yet minor update in anticipation of the frontend release.

The major change is a complete redesign of (and drop-in replacement for) Bruce,
and a bunch of frontend related improvements

### New

 - New and not new: Bruce is completely redesigned from scratch
 - The `Pattern::setOptionIfUnset` and `Pattern::setValueIfUnset` methods allow child patterns to override options and values
 - The `Pattern::stretchToScale` method makes stretch options more intuitive
 - The `Part::circlesCross` and `Part::circleCrossesLine` methods find intersections of circles and lines
 - The `Part::shiftOutwards`, `Part::shiftFractionTowards`, and `Part::shiftFractionAlong` methods are additional shift helper methods
 - The `Utils::constraint` method is new. It deals with input validation for options
 - Pattern config files have new data used by the frontend, including `handle`, `tags` and a `title`, `description`, and `group` for options. Only impacts the frontend
 - Pattern config files also have conditional options, using `dependsOn` and `onlyOn` in the options config. Only impacts the frontend
 - The info service will hide all pattern parts that have a title starting with a `.` character
 - PHP error output is now disabled in index.php so we don't have to trust the server config
 - Added the frontend draft handle to the Core theme so that the frontend reference is printed on the pattern
 - The standard theme has a new `various` path CSS class
 - Added support for custom seam allowance to the Freesewing channel and to all patterns

### Changes

 - Removed all beta patterns from the core repository
 - Changes to the deploy script and Travis configuration
 - Changed the `sleeveLengthToWrist` measurement to `shoulderToWrist`
 - Changed the `setOption` method to `setOptionIfUnset` in all patterns
 - Part titles no longer use CSS transforms as they turned out to be unreliable when rendered by the browser or as PDF
 - Added the `freesewing` class to SVG output to avoid style collisions
 - Simplified the logo
 - Updated the apigen config for the auto-generated class documentation
 - The `Part::newTextOnPath` method takes an extra `$renderPath` parameter
 - Simon's back is cut on the fold now, reducing the pattern's print size

#### Brian

 - The range of the `sleevecapHeight` options has been tweaked
 - The `shoulderSlope` option is now configurable. (also caused minor changes in Aaron, Sven and Wahid)

#### Bruce
 - Changed `stretchFactor` options to `stretch`

#### Cathrin

 - Now uses `hipsCircumference` as measurement, like other patterns (was `hips`)
 - Now uses `naturalWaistToHip` as measurement, like other patterns (was `naturalWaistToHips`)

#### Simon

 - Removed `backNeckCutout` option
 - Changed to more sensible defaults for ease options

#### Theo & Theodore

 - Renamed the `waistbandRise` option to `backRise`
 - Renamed the `bodyRise` option to `seatDepth`

#### Wahid

 - Updated the (faulty) cutting instructions for the front

### Fixes

 - Bug fix in the `Utils::lineLineIntersection` method when dealing with parallel lines

#### Simon

 - Fixed faulty part number on the sleeve part
 - The `shoulderSlope` option was incorrectly set to `measure`, rather than `angle`
 - Fixed issue with cuff drape and nr of pleats

#### Theo & Theodore

 - Both patterns now take the `lengthBonus` option into account

## Version 1.0.1

**Release date**: 2017-04-11

This release fixes is crippling typo in package.json and removes 
a tutorial pattern from the core repository.

### Breaking changes

#### Removed the BabyBib pattern

The BabyBib pattern is the subject of our pattern design tutorial in
our documention where we are re-creating it step by step.

Having it in core defeats the point of the tutorial because right 
from the start the entire pattern is there.
So, we've pulled it from core, and are keeping it in its own
[BabyBib repository](https://github.com/freesewing/BabyBib)
for reference.

This is only a breaking change if you were depending on or
extending the BabyBib pattern. Which is unlikely at this
point.

> **Tip:** If you need the BabyBib pattern, you can include it as a Git
submodule.

### Changes

#### Added Packagist info to package.json

The `package.json` file was missing some info that is used 
by Packagist (the PHP package manager). This has been added.

### Fixes

- Fixes an unclosed bracket in composer.json (that was embarassing and 
reason for a new release)

## Version 1.0.0

**Release date**: 2017-01-26

This is the first public release of freesewing.
This changelog is relative to this baseline.

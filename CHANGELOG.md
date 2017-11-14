# Change Log

> All notable changes to freesewing core should be be documented in this file.
> Freesewing uses [Semantic Versioning](http://semver.org/).

## Version 1.2.4

**Release date**: 2017-11-14

This release includes the Benjamin Bow Tie pattern by @woutervdub. 
It's our first ever community pattern, and still in beta for now.
More details in 
[the announcement blog post](https://freesewing.org/blog/benjamin-bow-tie-beta/).

### New

 - Benjamin is a new pattern, a bow tie. It's currently (still) in the Beta namespace.
 - The path.lashed CSS class is new in the default theme. It draws a line with long dashes (lashes).
 - The `Utils::asPointArray` methods is new, it returns an array of all points used in a path string
 - Pattern configuration files now can include `seamAllowance` section to set default seam allowance

### Changes

  - Streamlined pattern configuration changes, removing non-relevant info

#### Huey
 
  - Added sleeve notches to Huey

### Fixes

#### Simon

  - Fixed the instructions for cutting out interfacing in Simon

## Version 1.2.3

**Release date**: 2017-10-24

This release fixes an option in Aaron and the pocket back of Wahid.

It also adds some community things such as code of conduct and other GitHub community files.

### Fixes

#### Aaron

 - The `shoulderStrapPlacement` options caused issues when it was zero. It's boundaries have been set to avoid that.
 - The pocket bag in Wahid had a leftover welt line and notches outside the part.

## Version 1.2.2

**Release date**: 2017-10-23

This release fixes the offset in the DesignTutorial pattern which was incorrect.

Path offset changed direction in core v1.2.0. At that time, all patterns were updated to
reflect these changes, but the DesignTutorial pattern was overlooked.

### Fixes

#### DesignTutorial

 - Switch pathOffset from -3 to 3

## Version 1.2.1

**Release date**: 2017-10-21

This release includes the HueyHoodie pattern. It's stil in the beta namespace for now.
[This blog post](https://freesewing.org/blog/huey-hoodie-beta/) has more info on what 
it means for a pattern to be in beta.

### New

 - Huey is a new pattern, a zip-up hoodie for men. It's currently in the Beta namespace

### Changes

#### More modes for addTitle

The `Part::addTite` method now accepts three new modes:

 - extrasmall
 - horizontal-extrasmall
 - vertical-extrasmall

Thanks to @Woutervdub for suggesting this and his pull request.

#### Config file layout changes

The config files had a few minor tweaks related to the frontend integration.

 - Descriptions have been updated
 - inMemoryOf is moved to the info section

### Fixes

#### Wahid

 - Fixed a few typos
 - Fixed pocket bag and pocket facing which had their lengths mixed up.

Thanks to @PD75 for reporting these issues in Wahid.

#### Simon

 - Fixed the buttonfree lenght that was off
 - Changed the button layout so that the top button is closer to the collar

Thanks to @Woutervdub for contributing code for this.  
Thanks to @Stefan1960 for reporting these issues in Simon.

## Version 1.2.0

**Release date**: 2017-10-05

This release fixes an issue with the `Part::angle()` method, and deals with the ripple effects of that change.

It also includes some small improvements to various patterns.

### Changes

#### Part::angle()
The `Part::angle()` method returned results that were 180 degrees off. That's fixed now

> This is kind of a big deal, because angle() is used in a lot of places,
> so there are a bunch of updates to fix them all.
> While this bug should have never been there, I suspect it was introduced
> when trying to work out what quadrant of a circle an angle sits in.
>
> While doing that, it's likely that I got confused thinking that a positive
> delta on the Y-axis puts the angle in the top quadrant (as common sense
> would dictate) but that I lost track for a moment of the fact that in SVG
> the coordinate system works differently, and the Y-axis increments downwards.

All patterns that ship with freesewing core have been updated to handle these changes.
If you have your own patterns, they will need updating too. You should look out for:

 - Any place you use the `Part::angle()` method
 - All path offset methods. The offset is shifted 180 degrees, so multiply your offset by -1

The changes in `Part::angle()` also triggered some changes in other `Part` methods that depend on it.

This is also the change that caused the bump in the minor version.

#### Part::shiftTowards

This method was rewritten to be more concise.

#### Pattern::requested
The new `Pattern::requested()` method contains the full class name of the pattern that was 
requested by the user. 

This is handy for comparison to PHP's magic `__CLASS__` variable to figure out whether your
pattern is the pattern requested by the user, or merely a stepping stone to get there (because
the requested pattern is a (grand)child).

### Fixes

#### Aaron

 - Removed a division by zero check after updating the config file to no longer allow zero.

#### Brian

 - Only true seams if Brian is the requested pattern

#### Simon

 - Prevent a missing `sa` parameter from throwing a warning.

#### Sven

 - Fix issue where the hip measurements wasn't used, but only the chest measurement

#### Theo

 - Prevent a missing `sa` parameter from throwing a warning.
 - Used small titles for smaller pattern parts

#### Theodore

 - Prevent a missing `sa` parameter from throwing a warning.
 - Used small titles for smaller pattern parts

#### Part::addTitle

 - Fixed rendering issue in vertical titles



Prevent a missing `sa` parameter from throwing a warning.

## Version 1.1.3

**Release date**: 2017-09-29

This is a bugfix release.

### Fixes

#### Simon

 - The hip measurement and ease were not taken into account, hip was identical to chest circumference and ease

## Version 1.1.2

**Release date**: 2017-09-17

This is, apart from some tweaks to Simon's defaults, a bugfix release.

### Changes

#### Simon

 - Changed the defaults for some options to make them more sensible

### Fixes

#### Simon

 - The lenth bonus was counted double, once in Brian, then once again in Simon
 - Fixed a seam allowance that was assigned a `canvas` rather than `fabric` class

## Version 1.1.1

**Release date**: 2017-09-13

This is a bugfix release.

### Fixes

#### Simon

 - The seam allowance at the hem was incorrect when the lenthBonus was very low.
 - The cut in the sleeve for the placket was too short
 - There was a problem with the seam allowance at the buttonhole placket

#### CLI

 - The link to the documentation site has been updated

## Version 1.1.0

**Release date**: 2017-08-24

This is a non-trivial yet minor update in anticipation of the frontend release.

The major changes are a complete redesign of (and drop-in replacement for) Bruce,
and a bunch of frontend related improvements.

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
 - The NotationLegend pattern in the Docs namespace is new. It's used in the documentation.

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
 - Fixed incorrect release date for v1.0.0 in this changelog

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

**Release date**: 2017-03-24

This is the first public release of freesewing.
This changelog is relative to this baseline.

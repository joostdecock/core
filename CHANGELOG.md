# Change Log

> All notable changes to freesewing core should be be documented in this file.
> Freesewing does [Semantic Versioning](http://semver.org/).

## Unreleased Version 1.1.0

### Fixes

Fixes an unclosed bracket in composer.json (that was embarassing)

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
extending the BabyBib pattern. While that's unlikely at this
point, it is why we're bumping up the minor version on this release.

**tip:** If you need the BabyBib pattern, you can include it as a Git
submodule.

### Changes

#### Added Packagist info to package.json

The `package.json` file was missing some info that is used 
by Packagist (the PHP package manager). This has been added.


## Version 1.0.0

**Release date**: 2017-01-26

This is the first public release of freesewing.
This changelog is relative to this baseline.

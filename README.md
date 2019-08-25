# DEPRECATED: Please see freesewing/freesewing instead

FreeSewing v2 and up are maintained in the [freesewing](https://github.com/freesewing/freesewing) repository.


<a href="https://freesewing.org/"><img src="https://freesewing.org/img/logo/black.svg" align="right" width=200 style="max-width: 20%;" /></a>
[![Build Status](https://travis-ci.org/freesewing/core.svg?branch=master)](https://travis-ci.org/freesewing/core)

# Freesewing core
[Freesewing](https://freesewing.org/) is an online platform to draft sewing patterns based on your measurements.

> This is the core repository, which holds the platform and the sewing patterns I maintain.

For all info on what freesewing does/is/provides, please check [the about page](https://freesewing.org/about/) or  [documentation](https://freesewing.org/docs/).

## System Requirements
* PHP 5.6 or newer
* composer

## Installation

Full install instructions are available at [freesewing.org/docs/core/install](https://freesewing.org/docs/core/install)
but here's the gist of it:

> ##### A note about installing on PHP 5.6
>
> Core uses a Developer theme that requires PHP 7.0 or newer. However, that theme is only 
> useful for hacking core. So if you just want to run this and maybe design some patterns
> install without the dev requirements. 
>
> In other words, to install on PHP 5.6, pass `--no-dev` to composer

### From packagist
```
composer create-project freesewing/core
cd core
composer dump-autoload -o
```

### From GitHub
```
git clone git@github.com:freesewing/core.git
cd core
composer install
composer dump-autoload -o
```

## License
This code is licensed [GPL-3](https://www.gnu.org/licenses/gpl-3.0.en.html), 
the pattern drafts, documentation, and other content is licensed [CC-BY](https://creativecommons.org/licenses/by/4.0/).

## Contribute

Your pull request are welcome here. 

If you're interested in contributing, I'd love your help.
That's exactly why I made this thing open source in the first place.

Read [freesewing.org/contribute](https://freesewing.org/contribute) to get started.
If you have any questions, the best place to ask is [the freesewing community on Gitter](https://gitter.im/freesewing/freesewing).

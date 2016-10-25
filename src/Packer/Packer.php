<?php
/** Freesewing\Packer class */
namespace Freesewing;

/**
 * Abstract class for packers.
 *
 * This abstract class makes it easy to plug in a different packer.
 * This is here because one day, I'd like to write a more efficient packer.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
abstract class Packer
{
    /**
     * Packers should implement the fit() method
     *
     * @param array List of blocks to fit
     */
    abstract public function fit(&$blocks);
}

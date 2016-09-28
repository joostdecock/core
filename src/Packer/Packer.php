<?php

namespace Freesewing;

/**
 * Freesewing\Packer class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
abstract class Packer
{
    abstract public function fit(&$blocks);
}

<?php

namespace Freesewing\Channels;

/**
 * Freesewing\Channels\Channel class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Channel
{
    public function isValidRequest($requestData)
    {
        return true;
    }

    public function standardizeModelMeasurements($requestData)
    {
        return array();
    }

    public function standardizePatternOptions($requestData)
    {
        return array();
    }

    public function cleanUp()
    {
    }
}

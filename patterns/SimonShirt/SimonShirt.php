<?php

namespace Freesewing\Patterns;

/**
 * Freesewing\Patterns\SimonShirt class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class SimonShirt extends JoostBodyBlock
{
    private $config_file = __DIR__.'/config.yml';
    public $parts = array();

    public function draft($model)
    {
        $this->msg('this is a test');
        $this->msg('this is another test');
        $this->help = array();
        $this->help['armholeDepth'] = 200 + ($model->getMeasurement('shoulderSlope')/2 - 27.5) + ($model->getMeasurement('upperBicepsCircumference')/10);
        $this->help['collarShapeFactor'] = 1;
        $this->help['sleevecapShapeFactor'] = 1;
        
        $this->loadParts();
        
        $this->draftBack($model);
        $this->draftFront($model);
        $this->draftSleeve($model);
    }

}

<?php
/** Freesewing\Patterns\OffsetTest class */
namespace Freesewing\Patterns;

use Freesewing\Utils;
use Freesewing\BezierToolbox;

/**
 * A pattern template
 *
 * If you'd like to add you own pattern, you can copy this class/directory.
 * It's an empty skeleton for you to start working with
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2017 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class OffsetTest extends Pattern
{
    public function sample($model)
    {
        return true;
    }

    public function draft($model)
    {
        /** @var \Freesewing\Part $p */
        $start = microtime(true);
        $segments = 0;
        for($i=1;$i<6;$i++) {
            for($j=1;$j<6;$j++) {
                for($k=1;$k<6;$k++) {
                    $id = "$i.$j.$k";
                    $this->newPart($id);
                    $p = $this->parts[$id];
                    $p->newPoint("$id-cp1", $i*-10, 0);
                    $p->addPoint("$id-cp2", $p->flipX("$id-cp1"));
                    $p->addPoint("$id-start", $p->shift("$id-cp1",-90 - $j*9,$k*10));
                    $p->addPoint("$id-end", $p->flipX("$id-start"));
                    $p->newPath($id, "M $id-start C $id-cp1 $id-cp2 $id-end");
                    $p->offsetPath("$id-offsetIn", $id, 10, 1, ['class' => 'stroke-sm stroke-note']);
                    $p->offsetPath("$id-offsetOut", $id, -10, 1, ['class' => 'stroke-sm stroke-warning']);
                    $p->newTextOnPath(
                        $p->newId(),
                        "M $id-start C $id-cp1 $id-cp2 $id-end", 
                        count($p->paths["$id-offsetIn"]->breakUp()).' segments',
                        ['class' => 'text-center text-sm fill-note', 'dy' => 8]
                    );
                    $p->newTextOnPath(
                        $p->newId(),
                        "M $id-start C $id-cp1 $id-cp2 $id-end", 
                        count($p->paths["$id-offsetOut"]->breakUp()).' segments',
                        ['class' => 'text-center text-sm fill-warning', 'dy' => -6]
                    );
                    $segments += count($p->paths["$id-offsetIn"]->breakUp());
                    $segments += count($p->paths["$id-offsetOut"]->breakUp());
                }
            }
        }
        $end = microtime(true);
        $tt = round($end-$start,2);
        $this->newPart('time');
        $p = $this->parts['time'];
        $p->newPoint(1,0,0);
        $p->newPoint(2,$tt*40,0);
        $p->newPath(1,'M 1 L 2');
        $p->newLinearDimension(1, 2, 10, "Time taken: $tt seconds - $segments segments in total");
        $this->newPart('th');
        $p = $this->parts['th'];
        $p->newPoint(1, 0,10);
        $p->newPoint(2, 30, 0);
        $p->newPoint(3, 70, 5);
        $p->newPoint(4, 120, 50);
        /*       

        $p->newPath(1,'M 1 C 2 3 4');
        $p->NEW_offsetPath($p->newId(),1,10, ['class' => 'stroke-note']);
        $p->NEW_offsetPath($p->newId(),1,-10, ['class' => 'stroke-note']);
        $p->NEW_offsetPath($p->newId(),1,20, ['class' => 'stroke-note']);
        $p->NEW_offsetPath($p->newId(),1,-20, ['class' => 'stroke-note']);
        $p->NEW_offsetPath($p->newId(),1,30, ['class' => 'stroke-note']);
        $p->NEW_offsetPath($p->newId(),1,-30, ['class' => 'stroke-note']);
        $p->NEW_offsetPath($p->newId(),1,40, ['class' => 'stroke-note']);
        $p->NEW_offsetPath($p->newId(),1,-40, ['class' => 'stroke-note']);
        $p->NEW_offsetPath($p->newId(),1,50, ['class' => 'stroke-note']);
        $p->NEW_offsetPath($p->newId(),1,-50, ['class' => 'stroke-note']);
        $this->newPart('th');
        $p = $this->parts['th'];
        $p->newPoint(1, 10,10);
        $p->newPoint(2, 5, 5);
        $p->newPoint(3, 150, 5);
        $p->newPoint(4, 155, 10);

        $p->newPath(1,'M 2 C 1 3 4');
        $p->NEW_offsetPath($p->newId(),1,10, ['class' => 'stroke-note']);
        */
    }
}

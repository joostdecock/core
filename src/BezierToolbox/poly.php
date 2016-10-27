<?php
        $poly [] = -$c13x3*$c23y3 + $c13y3*$c23x3 - 3*$c13->getX()*$c13y2*$c23x2*$c23->getY() +
        3*$c13x2*$c13->getY()*$c23->getX()*$c23y2;

        $poly [] = -6*$c13->getX()*$c22->getX()*$c13y2*$c23->getX()*$c23->getY() + 6*$c13x2*$c13->getY()*$c22->getY()*$c23->getX()*$c23->getY() + 3*$c22->getX()*$c13y3*$c23x2 -
            3*$c13x3*$c22->getY()*$c23y2 - 3*$c13->getX()*$c13y2*$c22->getY()*$c23x2 + 3*$c13x2*$c22->getX()*$c13->getY()*$c23y2;

        $poly [] = -6*$c21->getX()*$c13->getX()*$c13y2*$c23->getX()*$c23->getY() - 6*$c13->getX()*$c22->getX()*$c13y2*$c22->getY()*$c23->getX() + 6*$c13x2*$c22->getX()*$c13->getY()*$c22->getY()*$c23->getY() +
            3*$c21->getX()*$c13y3*$c23x2 + 3*$c22x2*$c13y3*$c23->getX() + 3*$c21->getX()*$c13x2*$c13->getY()*$c23y2 - 3*$c13->getX()*$c21->getY()*$c13y2*$c23x2 -
            3*$c13->getX()*$c22x2*$c13y2*$c23->getY() + $c13x2*$c13->getY()*$c23->getX()*(6*$c21->getY()*$c23->getY() + 3*$c22y2) + $c13x3*(-$c21->getY()*$c23y2 -
            2*$c22y2*$c23->getY() - $c23->getY()*(2*$c21->getY()*$c23->getY() + $c22y2));

        $poly [] = $c11->getX()*$c12->getY()*$c13->getX()*$c13->getY()*$c23->getX()*$c23->getY() - $c11->getY()*$c12->getX()*$c13->getX()*$c13->getY()*$c23->getX()*$c23->getY() + 6*$c21->getX()*$c22->getX()*$c13y3*$c23->getX() +
            3*$c11->getX()*$c12->getX()*$c13->getX()*$c13->getY()*$c23y2 + 6*$c10->getX()*$c13->getX()*$c13y2*$c23->getX()*$c23->getY() - 3*$c11->getX()*$c12->getX()*$c13y2*$c23->getX()*$c23->getY() -
            3*$c11->getY()*$c12->getY()*$c13->getX()*$c13->getY()*$c23x2 - 6*$c10->getY()*$c13x2*$c13->getY()*$c23->getX()*$c23->getY() - 6*$c20->getX()*$c13->getX()*$c13y2*$c23->getX()*$c23->getY() +
            3*$c11->getY()*$c12->getY()*$c13x2*$c23->getX()*$c23->getY() - 2*$c12->getX()*$c12y2*$c13->getX()*$c23->getX()*$c23->getY() - 6*$c21->getX()*$c13->getX()*$c22->getX()*$c13y2*$c23->getY() -
            6*$c21->getX()*$c13->getX()*$c13y2*$c22->getY()*$c23->getX() - 6*$c13->getX()*$c21->getY()*$c22->getX()*$c13y2*$c23->getX() + 6*$c21->getX()*$c13x2*$c13->getY()*$c22->getY()*$c23->getY() +
            2*$c12x2*$c12->getY()*$c13->getY()*$c23->getX()*$c23->getY() + $c22x3*$c13y3 - 3*$c10->getX()*$c13y3*$c23x2 + 3*$c10->getY()*$c13x3*$c23y2 +
            3*$c20->getX()*$c13y3*$c23x2 + $c12y3*$c13->getX()*$c23x2 - $c12x3*$c13->getY()*$c23y2 - 3*$c10->getX()*$c13x2*$c13->getY()*$c23y2 +
            3*$c10->getY()*$c13->getX()*$c13y2*$c23x2 - 2*$c11->getX()*$c12->getY()*$c13x2*$c23y2 + $c11->getX()*$c12->getY()*$c13y2*$c23x2 - $c11->getY()*$c12->getX()*$c13x2*$c23y2 +
            2*$c11->getY()*$c12->getX()*$c13y2*$c23x2 + 3*$c20->getX()*$c13x2*$c13->getY()*$c23y2 - $c12->getX()*$c12y2*$c13->getY()*$c23x2 -
            3*$c20->getY()*$c13->getX()*$c13y2*$c23x2 + $c12x2*$c12->getY()*$c13->getX()*$c23y2 - 3*$c13->getX()*$c22x2*$c13y2*$c22->getY() +
            $c13x2*$c13->getY()*$c23->getX()*(6*$c20->getY()*$c23->getY() + 6*$c21->getY()*$c22->getY()) + $c13x2*$c22->getX()*$c13->getY()*(6*$c21->getY()*$c23->getY() + 3*$c22y2) +
            $c13x3*(-2*$c21->getY()*$c22->getY()*$c23->getY() - $c20->getY()*$c23y2 - $c22->getY()*(2*$c21->getY()*$c23->getY() + $c22y2) - $c23->getY()*(2*$c20->getY()*$c23->getY() + 2*$c21->getY()*$c22->getY()));

        $poly [] = 6*$c11->getX()*$c12->getX()*$c13->getX()*$c13->getY()*$c22->getY()*$c23->getY() + $c11->getX()*$c12->getY()*$c13->getX()*$c22->getX()*$c13->getY()*$c23->getY() + $c11->getX()*$c12->getY()*$c13->getX()*$c13->getY()*$c22->getY()*$c23->getX() -
            $c11->getY()*$c12->getX()*$c13->getX()*$c22->getX()*$c13->getY()*$c23->getY() - $c11->getY()*$c12->getX()*$c13->getX()*$c13->getY()*$c22->getY()*$c23->getX() - 6*$c11->getY()*$c12->getY()*$c13->getX()*$c22->getX()*$c13->getY()*$c23->getX() -
            6*$c10->getX()*$c22->getX()*$c13y3*$c23->getX() + 6*$c20->getX()*$c22->getX()*$c13y3*$c23->getX() + 6*$c10->getY()*$c13x3*$c22->getY()*$c23->getY() + 2*$c12y3*$c13->getX()*$c22->getX()*$c23->getX() -
            2*$c12x3*$c13->getY()*$c22->getY()*$c23->getY() + 6*$c10->getX()*$c13->getX()*$c22->getX()*$c13y2*$c23->getY() + 6*$c10->getX()*$c13->getX()*$c13y2*$c22->getY()*$c23->getX() +
            6*$c10->getY()*$c13->getX()*$c22->getX()*$c13y2*$c23->getX() - 3*$c11->getX()*$c12->getX()*$c22->getX()*$c13y2*$c23->getY() - 3*$c11->getX()*$c12->getX()*$c13y2*$c22->getY()*$c23->getX() +
            2*$c11->getX()*$c12->getY()*$c22->getX()*$c13y2*$c23->getX() + 4*$c11->getY()*$c12->getX()*$c22->getX()*$c13y2*$c23->getX() - 6*$c10->getX()*$c13x2*$c13->getY()*$c22->getY()*$c23->getY() -
            6*$c10->getY()*$c13x2*$c22->getX()*$c13->getY()*$c23->getY() - 6*$c10->getY()*$c13x2*$c13->getY()*$c22->getY()*$c23->getX() - 4*$c11->getX()*$c12->getY()*$c13x2*$c22->getY()*$c23->getY() -
            6*$c20->getX()*$c13->getX()*$c22->getX()*$c13y2*$c23->getY() - 6*$c20->getX()*$c13->getX()*$c13y2*$c22->getY()*$c23->getX() - 2*$c11->getY()*$c12->getX()*$c13x2*$c22->getY()*$c23->getY() +
            3*$c11->getY()*$c12->getY()*$c13x2*$c22->getX()*$c23->getY() + 3*$c11->getY()*$c12->getY()*$c13x2*$c22->getY()*$c23->getX() - 2*$c12->getX()*$c12y2*$c13->getX()*$c22->getX()*$c23->getY() -
            2*$c12->getX()*$c12y2*$c13->getX()*$c22->getY()*$c23->getX() - 2*$c12->getX()*$c12y2*$c22->getX()*$c13->getY()*$c23->getX() - 6*$c20->getY()*$c13->getX()*$c22->getX()*$c13y2*$c23->getX() -
            6*$c21->getX()*$c13->getX()*$c21->getY()*$c13y2*$c23->getX() - 6*$c21->getX()*$c13->getX()*$c22->getX()*$c13y2*$c22->getY() + 6*$c20->getX()*$c13x2*$c13->getY()*$c22->getY()*$c23->getY() +
            2*$c12x2*$c12->getY()*$c13->getX()*$c22->getY()*$c23->getY() + 2*$c12x2*$c12->getY()*$c22->getX()*$c13->getY()*$c23->getY() + 2*$c12x2*$c12->getY()*$c13->getY()*$c22->getY()*$c23->getX() +
            3*$c21->getX()*$c22x2*$c13y3 + 3*$c21x2*$c13y3*$c23->getX() - 3*$c13->getX()*$c21->getY()*$c22x2*$c13y2 - 3*$c21x2*$c13->getX()*$c13y2*$c23->getY() +
            $c13x2*$c22->getX()*$c13->getY()*(6*$c20->getY()*$c23->getY() + 6*$c21->getY()*$c22->getY()) + $c13x2*$c13->getY()*$c23->getX()*(6*$c20->getY()*$c22->getY() + 3*$c21y2) +
            $c21->getX()*$c13x2*$c13->getY()*(6*$c21->getY()*$c23->getY() + 3*$c22y2) + $c13x3*(-2*$c20->getY()*$c22->getY()*$c23->getY() - $c23->getY()*(2*$c20->getY()*$c22->getY() + $c21y2) -
            $c21->getY()*(2*$c21->getY()*$c23->getY() + $c22y2) - $c22->getY()*(2*$c20->getY()*$c23->getY() + 2*$c21->getY()*$c22->getY()));

        $poly [] = $c11->getX()*$c21->getX()*$c12->getY()*$c13->getX()*$c13->getY()*$c23->getY() + $c11->getX()*$c12->getY()*$c13->getX()*$c21->getY()*$c13->getY()*$c23->getX() + $c11->getX()*$c12->getY()*$c13->getX()*$c22->getX()*$c13->getY()*$c22->getY() -
            $c11->getY()*$c12->getX()*$c21->getX()*$c13->getX()*$c13->getY()*$c23->getY() - $c11->getY()*$c12->getX()*$c13->getX()*$c21->getY()*$c13->getY()*$c23->getX() - $c11->getY()*$c12->getX()*$c13->getX()*$c22->getX()*$c13->getY()*$c22->getY() -
            6*$c11->getY()*$c21->getX()*$c12->getY()*$c13->getX()*$c13->getY()*$c23->getX() - 6*$c10->getX()*$c21->getX()*$c13y3*$c23->getX() + 6*$c20->getX()*$c21->getX()*$c13y3*$c23->getX() +
            2*$c21->getX()*$c12y3*$c13->getX()*$c23->getX() + 6*$c10->getX()*$c21->getX()*$c13->getX()*$c13y2*$c23->getY() + 6*$c10->getX()*$c13->getX()*$c21->getY()*$c13y2*$c23->getX() +
            6*$c10->getX()*$c13->getX()*$c22->getX()*$c13y2*$c22->getY() + 6*$c10->getY()*$c21->getX()*$c13->getX()*$c13y2*$c23->getX() - 3*$c11->getX()*$c12->getX()*$c21->getX()*$c13y2*$c23->getY() -
            3*$c11->getX()*$c12->getX()*$c21->getY()*$c13y2*$c23->getX() - 3*$c11->getX()*$c12->getX()*$c22->getX()*$c13y2*$c22->getY() + 2*$c11->getX()*$c21->getX()*$c12->getY()*$c13y2*$c23->getX() +
            4*$c11->getY()*$c12->getX()*$c21->getX()*$c13y2*$c23->getX() - 6*$c10->getY()*$c21->getX()*$c13x2*$c13->getY()*$c23->getY() - 6*$c10->getY()*$c13x2*$c21->getY()*$c13->getY()*$c23->getX() -
            6*$c10->getY()*$c13x2*$c22->getX()*$c13->getY()*$c22->getY() - 6*$c20->getX()*$c21->getX()*$c13->getX()*$c13y2*$c23->getY() - 6*$c20->getX()*$c13->getX()*$c21->getY()*$c13y2*$c23->getX() -
            6*$c20->getX()*$c13->getX()*$c22->getX()*$c13y2*$c22->getY() + 3*$c11->getY()*$c21->getX()*$c12->getY()*$c13x2*$c23->getY() - 3*$c11->getY()*$c12->getY()*$c13->getX()*$c22x2*$c13->getY() +
            3*$c11->getY()*$c12->getY()*$c13x2*$c21->getY()*$c23->getX() + 3*$c11->getY()*$c12->getY()*$c13x2*$c22->getX()*$c22->getY() - 2*$c12->getX()*$c21->getX()*$c12y2*$c13->getX()*$c23->getY() -
            2*$c12->getX()*$c21->getX()*$c12y2*$c13->getY()*$c23->getX() - 2*$c12->getX()*$c12y2*$c13->getX()*$c21->getY()*$c23->getX() - 2*$c12->getX()*$c12y2*$c13->getX()*$c22->getX()*$c22->getY() -
            6*$c20->getY()*$c21->getX()*$c13->getX()*$c13y2*$c23->getX() - 6*$c21->getX()*$c13->getX()*$c21->getY()*$c22->getX()*$c13y2 + 6*$c20->getY()*$c13x2*$c21->getY()*$c13->getY()*$c23->getX() +
            2*$c12x2*$c21->getX()*$c12->getY()*$c13->getY()*$c23->getY() + 2*$c12x2*$c12->getY()*$c21->getY()*$c13->getY()*$c23->getX() + 2*$c12x2*$c12->getY()*$c22->getX()*$c13->getY()*$c22->getY() -
            3*$c10->getX()*$c22x2*$c13y3 + 3*$c20->getX()*$c22x2*$c13y3 + 3*$c21x2*$c22->getX()*$c13y3 + $c12y3*$c13->getX()*$c22x2 +
            3*$c10->getY()*$c13->getX()*$c22x2*$c13y2 + $c11->getX()*$c12->getY()*$c22x2*$c13y2 + 2*$c11->getY()*$c12->getX()*$c22x2*$c13y2 -
            $c12->getX()*$c12y2*$c22x2*$c13->getY() - 3*$c20->getY()*$c13->getX()*$c22x2*$c13y2 - 3*$c21x2*$c13->getX()*$c13y2*$c22->getY() +
            $c12x2*$c12->getY()*$c13->getX()*(2*$c21->getY()*$c23->getY() + $c22y2) + $c11->getX()*$c12->getX()*$c13->getX()*$c13->getY()*(6*$c21->getY()*$c23->getY() + 3*$c22y2) +
            $c21->getX()*$c13x2*$c13->getY()*(6*$c20->getY()*$c23->getY() + 6*$c21->getY()*$c22->getY()) + $c12x3*$c13->getY()*(-2*$c21->getY()*$c23->getY() - $c22y2) +
            $c10->getY()*$c13x3*(6*$c21->getY()*$c23->getY() + 3*$c22y2) + $c11->getY()*$c12->getX()*$c13x2*(-2*$c21->getY()*$c23->getY() - $c22y2) +
            $c11->getX()*$c12->getY()*$c13x2*(-4*$c21->getY()*$c23->getY() - 2*$c22y2) + $c10->getX()*$c13x2*$c13->getY()*(-6*$c21->getY()*$c23->getY() - 3*$c22y2) +
            $c13x2*$c22->getX()*$c13->getY()*(6*$c20->getY()*$c22->getY() + 3*$c21y2) + $c20->getX()*$c13x2*$c13->getY()*(6*$c21->getY()*$c23->getY() + 3*$c22y2) +
            $c13x3*(-2*$c20->getY()*$c21->getY()*$c23->getY() - $c22->getY()*(2*$c20->getY()*$c22->getY() + $c21y2) - $c20->getY()*(2*$c21->getY()*$c23->getY() + $c22y2) -
            $c21->getY()*(2*$c20->getY()*$c23->getY() + 2*$c21->getY()*$c22->getY()));

        $poly [] = -$c10->getX()*$c11->getX()*$c12->getY()*$c13->getX()*$c13->getY()*$c23->getY() + $c10->getX()*$c11->getY()*$c12->getX()*$c13->getX()*$c13->getY()*$c23->getY() + 6*$c10->getX()*$c11->getY()*$c12->getY()*$c13->getX()*$c13->getY()*$c23->getX() -
            6*$c10->getY()*$c11->getX()*$c12->getX()*$c13->getX()*$c13->getY()*$c23->getY() - $c10->getY()*$c11->getX()*$c12->getY()*$c13->getX()*$c13->getY()*$c23->getX() + $c10->getY()*$c11->getY()*$c12->getX()*$c13->getX()*$c13->getY()*$c23->getX() +
            $c11->getX()*$c11->getY()*$c12->getX()*$c12->getY()*$c13->getX()*$c23->getY() - $c11->getX()*$c11->getY()*$c12->getX()*$c12->getY()*$c13->getY()*$c23->getX() + $c11->getX()*$c20->getX()*$c12->getY()*$c13->getX()*$c13->getY()*$c23->getY() +
            $c11->getX()*$c20->getY()*$c12->getY()*$c13->getX()*$c13->getY()*$c23->getX() + $c11->getX()*$c21->getX()*$c12->getY()*$c13->getX()*$c13->getY()*$c22->getY() + $c11->getX()*$c12->getY()*$c13->getX()*$c21->getY()*$c22->getX()*$c13->getY() -
            $c20->getX()*$c11->getY()*$c12->getX()*$c13->getX()*$c13->getY()*$c23->getY() - 6*$c20->getX()*$c11->getY()*$c12->getY()*$c13->getX()*$c13->getY()*$c23->getX() - $c11->getY()*$c12->getX()*$c20->getY()*$c13->getX()*$c13->getY()*$c23->getX() -
            $c11->getY()*$c12->getX()*$c21->getX()*$c13->getX()*$c13->getY()*$c22->getY() - $c11->getY()*$c12->getX()*$c13->getX()*$c21->getY()*$c22->getX()*$c13->getY() - 6*$c11->getY()*$c21->getX()*$c12->getY()*$c13->getX()*$c22->getX()*$c13->getY() -
            6*$c10->getX()*$c20->getX()*$c13y3*$c23->getX() - 6*$c10->getX()*$c21->getX()*$c22->getX()*$c13y3 - 2*$c10->getX()*$c12y3*$c13->getX()*$c23->getX() + 6*$c20->getX()*$c21->getX()*$c22->getX()*$c13y3 +
            2*$c20->getX()*$c12y3*$c13->getX()*$c23->getX() + 2*$c21->getX()*$c12y3*$c13->getX()*$c22->getX() + 2*$c10->getY()*$c12x3*$c13->getY()*$c23->getY() - 6*$c10->getX()*$c10->getY()*$c13->getX()*$c13y2*$c23->getX() +
            3*$c10->getX()*$c11->getX()*$c12->getX()*$c13y2*$c23->getY() - 2*$c10->getX()*$c11->getX()*$c12->getY()*$c13y2*$c23->getX() - 4*$c10->getX()*$c11->getY()*$c12->getX()*$c13y2*$c23->getX() +
            3*$c10->getY()*$c11->getX()*$c12->getX()*$c13y2*$c23->getX() + 6*$c10->getX()*$c10->getY()*$c13x2*$c13->getY()*$c23->getY() + 6*$c10->getX()*$c20->getX()*$c13->getX()*$c13y2*$c23->getY() -
            3*$c10->getX()*$c11->getY()*$c12->getY()*$c13x2*$c23->getY() + 2*$c10->getX()*$c12->getX()*$c12y2*$c13->getX()*$c23->getY() + 2*$c10->getX()*$c12->getX()*$c12y2*$c13->getY()*$c23->getX() +
            6*$c10->getX()*$c20->getY()*$c13->getX()*$c13y2*$c23->getX() + 6*$c10->getX()*$c21->getX()*$c13->getX()*$c13y2*$c22->getY() + 6*$c10->getX()*$c13->getX()*$c21->getY()*$c22->getX()*$c13y2 +
            4*$c10->getY()*$c11->getX()*$c12->getY()*$c13x2*$c23->getY() + 6*$c10->getY()*$c20->getX()*$c13->getX()*$c13y2*$c23->getX() + 2*$c10->getY()*$c11->getY()*$c12->getX()*$c13x2*$c23->getY() -
            3*$c10->getY()*$c11->getY()*$c12->getY()*$c13x2*$c23->getX() + 2*$c10->getY()*$c12->getX()*$c12y2*$c13->getX()*$c23->getX() + 6*$c10->getY()*$c21->getX()*$c13->getX()*$c22->getX()*$c13y2 -
            3*$c11->getX()*$c20->getX()*$c12->getX()*$c13y2*$c23->getY() + 2*$c11->getX()*$c20->getX()*$c12->getY()*$c13y2*$c23->getX() + $c11->getX()*$c11->getY()*$c12y2*$c13->getX()*$c23->getX() -
            3*$c11->getX()*$c12->getX()*$c20->getY()*$c13y2*$c23->getX() - 3*$c11->getX()*$c12->getX()*$c21->getX()*$c13y2*$c22->getY() - 3*$c11->getX()*$c12->getX()*$c21->getY()*$c22->getX()*$c13y2 +
            2*$c11->getX()*$c21->getX()*$c12->getY()*$c22->getX()*$c13y2 + 4*$c20->getX()*$c11->getY()*$c12->getX()*$c13y2*$c23->getX() + 4*$c11->getY()*$c12->getX()*$c21->getX()*$c22->getX()*$c13y2 -
            2*$c10->getX()*$c12x2*$c12->getY()*$c13->getY()*$c23->getY() - 6*$c10->getY()*$c20->getX()*$c13x2*$c13->getY()*$c23->getY() - 6*$c10->getY()*$c20->getY()*$c13x2*$c13->getY()*$c23->getX() -
            6*$c10->getY()*$c21->getX()*$c13x2*$c13->getY()*$c22->getY() - 2*$c10->getY()*$c12x2*$c12->getY()*$c13->getX()*$c23->getY() - 2*$c10->getY()*$c12x2*$c12->getY()*$c13->getY()*$c23->getX() -
            6*$c10->getY()*$c13x2*$c21->getY()*$c22->getX()*$c13->getY() - $c11->getX()*$c11->getY()*$c12x2*$c13->getY()*$c23->getY() - 2*$c11->getX()*$c11y2*$c13->getX()*$c13->getY()*$c23->getX() +
            3*$c20->getX()*$c11->getY()*$c12->getY()*$c13x2*$c23->getY() - 2*$c20->getX()*$c12->getX()*$c12y2*$c13->getX()*$c23->getY() - 2*$c20->getX()*$c12->getX()*$c12y2*$c13->getY()*$c23->getX() -
            6*$c20->getX()*$c20->getY()*$c13->getX()*$c13y2*$c23->getX() - 6*$c20->getX()*$c21->getX()*$c13->getX()*$c13y2*$c22->getY() - 6*$c20->getX()*$c13->getX()*$c21->getY()*$c22->getX()*$c13y2 +
            3*$c11->getY()*$c20->getY()*$c12->getY()*$c13x2*$c23->getX() + 3*$c11->getY()*$c21->getX()*$c12->getY()*$c13x2*$c22->getY() + 3*$c11->getY()*$c12->getY()*$c13x2*$c21->getY()*$c22->getX() -
            2*$c12->getX()*$c20->getY()*$c12y2*$c13->getX()*$c23->getX() - 2*$c12->getX()*$c21->getX()*$c12y2*$c13->getX()*$c22->getY() - 2*$c12->getX()*$c21->getX()*$c12y2*$c22->getX()*$c13->getY() -
            2*$c12->getX()*$c12y2*$c13->getX()*$c21->getY()*$c22->getX() - 6*$c20->getY()*$c21->getX()*$c13->getX()*$c22->getX()*$c13y2 - $c11y2*$c12->getX()*$c12->getY()*$c13->getX()*$c23->getX() +
            2*$c20->getX()*$c12x2*$c12->getY()*$c13->getY()*$c23->getY() + 6*$c20->getY()*$c13x2*$c21->getY()*$c22->getX()*$c13->getY() + 2*$c11x2*$c11->getY()*$c13->getX()*$c13->getY()*$c23->getY() +
            $c11x2*$c12->getX()*$c12->getY()*$c13->getY()*$c23->getY() + 2*$c12x2*$c20->getY()*$c12->getY()*$c13->getY()*$c23->getX() + 2*$c12x2*$c21->getX()*$c12->getY()*$c13->getY()*$c22->getY() +
            2*$c12x2*$c12->getY()*$c21->getY()*$c22->getX()*$c13->getY() + $c21x3*$c13y3 + 3*$c10x2*$c13y3*$c23->getX() - 3*$c10y2*$c13x3*$c23->getY() +
            3*$c20x2*$c13y3*$c23->getX() + $c11y3*$c13x2*$c23->getX() - $c11x3*$c13y2*$c23->getY() - $c11->getX()*$c11y2*$c13x2*$c23->getY() +
            $c11x2*$c11->getY()*$c13y2*$c23->getX() - 3*$c10x2*$c13->getX()*$c13y2*$c23->getY() + 3*$c10y2*$c13x2*$c13->getY()*$c23->getX() - $c11x2*$c12y2*$c13->getX()*$c23->getY() +
            $c11y2*$c12x2*$c13->getY()*$c23->getX() - 3*$c21x2*$c13->getX()*$c21->getY()*$c13y2 - 3*$c20x2*$c13->getX()*$c13y2*$c23->getY() + 3*$c20y2*$c13x2*$c13->getY()*$c23->getX() +
            $c11->getX()*$c12->getX()*$c13->getX()*$c13->getY()*(6*$c20->getY()*$c23->getY() + 6*$c21->getY()*$c22->getY()) + $c12x3*$c13->getY()*(-2*$c20->getY()*$c23->getY() - 2*$c21->getY()*$c22->getY()) +
            $c10->getY()*$c13x3*(6*$c20->getY()*$c23->getY() + 6*$c21->getY()*$c22->getY()) + $c11->getY()*$c12->getX()*$c13x2*(-2*$c20->getY()*$c23->getY() - 2*$c21->getY()*$c22->getY()) +
            $c12x2*$c12->getY()*$c13->getX()*(2*$c20->getY()*$c23->getY() + 2*$c21->getY()*$c22->getY()) + $c11->getX()*$c12->getY()*$c13x2*(-4*$c20->getY()*$c23->getY() - 4*$c21->getY()*$c22->getY()) +
            $c10->getX()*$c13x2*$c13->getY()*(-6*$c20->getY()*$c23->getY() - 6*$c21->getY()*$c22->getY()) + $c20->getX()*$c13x2*$c13->getY()*(6*$c20->getY()*$c23->getY() + 6*$c21->getY()*$c22->getY()) +
            $c21->getX()*$c13x2*$c13->getY()*(6*$c20->getY()*$c22->getY() + 3*$c21y2) + $c13x3*(-2*$c20->getY()*$c21->getY()*$c22->getY() - $c20y2*$c23->getY() -
            $c21->getY()*(2*$c20->getY()*$c22->getY() + $c21y2) - $c20->getY()*(2*$c20->getY()*$c23->getY() + 2*$c21->getY()*$c22->getY()));

        $poly [] = -$c10->getX()*$c11->getX()*$c12->getY()*$c13->getX()*$c13->getY()*$c22->getY() + $c10->getX()*$c11->getY()*$c12->getX()*$c13->getX()*$c13->getY()*$c22->getY() + 6*$c10->getX()*$c11->getY()*$c12->getY()*$c13->getX()*$c22->getX()*$c13->getY() -
            6*$c10->getY()*$c11->getX()*$c12->getX()*$c13->getX()*$c13->getY()*$c22->getY() - $c10->getY()*$c11->getX()*$c12->getY()*$c13->getX()*$c22->getX()*$c13->getY() + $c10->getY()*$c11->getY()*$c12->getX()*$c13->getX()*$c22->getX()*$c13->getY() +
            $c11->getX()*$c11->getY()*$c12->getX()*$c12->getY()*$c13->getX()*$c22->getY() - $c11->getX()*$c11->getY()*$c12->getX()*$c12->getY()*$c22->getX()*$c13->getY() + $c11->getX()*$c20->getX()*$c12->getY()*$c13->getX()*$c13->getY()*$c22->getY() +
            $c11->getX()*$c20->getY()*$c12->getY()*$c13->getX()*$c22->getX()*$c13->getY() + $c11->getX()*$c21->getX()*$c12->getY()*$c13->getX()*$c21->getY()*$c13->getY() - $c20->getX()*$c11->getY()*$c12->getX()*$c13->getX()*$c13->getY()*$c22->getY() -
            6*$c20->getX()*$c11->getY()*$c12->getY()*$c13->getX()*$c22->getX()*$c13->getY() - $c11->getY()*$c12->getX()*$c20->getY()*$c13->getX()*$c22->getX()*$c13->getY() - $c11->getY()*$c12->getX()*$c21->getX()*$c13->getX()*$c21->getY()*$c13->getY() -
            6*$c10->getX()*$c20->getX()*$c22->getX()*$c13y3 - 2*$c10->getX()*$c12y3*$c13->getX()*$c22->getX() + 2*$c20->getX()*$c12y3*$c13->getX()*$c22->getX() + 2*$c10->getY()*$c12x3*$c13->getY()*$c22->getY() -
            6*$c10->getX()*$c10->getY()*$c13->getX()*$c22->getX()*$c13y2 + 3*$c10->getX()*$c11->getX()*$c12->getX()*$c13y2*$c22->getY() - 2*$c10->getX()*$c11->getX()*$c12->getY()*$c22->getX()*$c13y2 -
            4*$c10->getX()*$c11->getY()*$c12->getX()*$c22->getX()*$c13y2 + 3*$c10->getY()*$c11->getX()*$c12->getX()*$c22->getX()*$c13y2 + 6*$c10->getX()*$c10->getY()*$c13x2*$c13->getY()*$c22->getY() +
            6*$c10->getX()*$c20->getX()*$c13->getX()*$c13y2*$c22->getY() - 3*$c10->getX()*$c11->getY()*$c12->getY()*$c13x2*$c22->getY() + 2*$c10->getX()*$c12->getX()*$c12y2*$c13->getX()*$c22->getY() +
            2*$c10->getX()*$c12->getX()*$c12y2*$c22->getX()*$c13->getY() + 6*$c10->getX()*$c20->getY()*$c13->getX()*$c22->getX()*$c13y2 + 6*$c10->getX()*$c21->getX()*$c13->getX()*$c21->getY()*$c13y2 +
            4*$c10->getY()*$c11->getX()*$c12->getY()*$c13x2*$c22->getY() + 6*$c10->getY()*$c20->getX()*$c13->getX()*$c22->getX()*$c13y2 + 2*$c10->getY()*$c11->getY()*$c12->getX()*$c13x2*$c22->getY() -
            3*$c10->getY()*$c11->getY()*$c12->getY()*$c13x2*$c22->getX() + 2*$c10->getY()*$c12->getX()*$c12y2*$c13->getX()*$c22->getX() - 3*$c11->getX()*$c20->getX()*$c12->getX()*$c13y2*$c22->getY() +
            2*$c11->getX()*$c20->getX()*$c12->getY()*$c22->getX()*$c13y2 + $c11->getX()*$c11->getY()*$c12y2*$c13->getX()*$c22->getX() - 3*$c11->getX()*$c12->getX()*$c20->getY()*$c22->getX()*$c13y2 -
            3*$c11->getX()*$c12->getX()*$c21->getX()*$c21->getY()*$c13y2 + 4*$c20->getX()*$c11->getY()*$c12->getX()*$c22->getX()*$c13y2 - 2*$c10->getX()*$c12x2*$c12->getY()*$c13->getY()*$c22->getY() -
            6*$c10->getY()*$c20->getX()*$c13x2*$c13->getY()*$c22->getY() - 6*$c10->getY()*$c20->getY()*$c13x2*$c22->getX()*$c13->getY() - 6*$c10->getY()*$c21->getX()*$c13x2*$c21->getY()*$c13->getY() -
            2*$c10->getY()*$c12x2*$c12->getY()*$c13->getX()*$c22->getY() - 2*$c10->getY()*$c12x2*$c12->getY()*$c22->getX()*$c13->getY() - $c11->getX()*$c11->getY()*$c12x2*$c13->getY()*$c22->getY() -
            2*$c11->getX()*$c11y2*$c13->getX()*$c22->getX()*$c13->getY() + 3*$c20->getX()*$c11->getY()*$c12->getY()*$c13x2*$c22->getY() - 2*$c20->getX()*$c12->getX()*$c12y2*$c13->getX()*$c22->getY() -
            2*$c20->getX()*$c12->getX()*$c12y2*$c22->getX()*$c13->getY() - 6*$c20->getX()*$c20->getY()*$c13->getX()*$c22->getX()*$c13y2 - 6*$c20->getX()*$c21->getX()*$c13->getX()*$c21->getY()*$c13y2 +
            3*$c11->getY()*$c20->getY()*$c12->getY()*$c13x2*$c22->getX() + 3*$c11->getY()*$c21->getX()*$c12->getY()*$c13x2*$c21->getY() - 2*$c12->getX()*$c20->getY()*$c12y2*$c13->getX()*$c22->getX() -
            2*$c12->getX()*$c21->getX()*$c12y2*$c13->getX()*$c21->getY() - $c11y2*$c12->getX()*$c12->getY()*$c13->getX()*$c22->getX() + 2*$c20->getX()*$c12x2*$c12->getY()*$c13->getY()*$c22->getY() -
            3*$c11->getY()*$c21x2*$c12->getY()*$c13->getX()*$c13->getY() + 6*$c20->getY()*$c21->getX()*$c13x2*$c21->getY()*$c13->getY() + 2*$c11x2*$c11->getY()*$c13->getX()*$c13->getY()*$c22->getY() +
            $c11x2*$c12->getX()*$c12->getY()*$c13->getY()*$c22->getY() + 2*$c12x2*$c20->getY()*$c12->getY()*$c22->getX()*$c13->getY() + 2*$c12x2*$c21->getX()*$c12->getY()*$c21->getY()*$c13->getY() -
            3*$c10->getX()*$c21x2*$c13y3 + 3*$c20->getX()*$c21x2*$c13y3 + 3*$c10x2*$c22->getX()*$c13y3 - 3*$c10y2*$c13x3*$c22->getY() + 3*$c20x2*$c22->getX()*$c13y3 +
            $c21x2*$c12y3*$c13->getX() + $c11y3*$c13x2*$c22->getX() - $c11x3*$c13y2*$c22->getY() + 3*$c10->getY()*$c21x2*$c13->getX()*$c13y2 -
            $c11->getX()*$c11y2*$c13x2*$c22->getY() + $c11->getX()*$c21x2*$c12->getY()*$c13y2 + 2*$c11->getY()*$c12->getX()*$c21x2*$c13y2 + $c11x2*$c11->getY()*$c22->getX()*$c13y2 -
            $c12->getX()*$c21x2*$c12y2*$c13->getY() - 3*$c20->getY()*$c21x2*$c13->getX()*$c13y2 - 3*$c10x2*$c13->getX()*$c13y2*$c22->getY() + 3*$c10y2*$c13x2*$c22->getX()*$c13->getY() -
            $c11x2*$c12y2*$c13->getX()*$c22->getY() + $c11y2*$c12x2*$c22->getX()*$c13->getY() - 3*$c20x2*$c13->getX()*$c13y2*$c22->getY() + 3*$c20y2*$c13x2*$c22->getX()*$c13->getY() +
            $c12x2*$c12->getY()*$c13->getX()*(2*$c20->getY()*$c22->getY() + $c21y2) + $c11->getX()*$c12->getX()*$c13->getX()*$c13->getY()*(6*$c20->getY()*$c22->getY() + 3*$c21y2) +
            $c12x3*$c13->getY()*(-2*$c20->getY()*$c22->getY() - $c21y2) + $c10->getY()*$c13x3*(6*$c20->getY()*$c22->getY() + 3*$c21y2) +
            $c11->getY()*$c12->getX()*$c13x2*(-2*$c20->getY()*$c22->getY() - $c21y2) + $c11->getX()*$c12->getY()*$c13x2*(-4*$c20->getY()*$c22->getY() - 2*$c21y2) +
            $c10->getX()*$c13x2*$c13->getY()*(-6*$c20->getY()*$c22->getY() - 3*$c21y2) + $c20->getX()*$c13x2*$c13->getY()*(6*$c20->getY()*$c22->getY() + 3*$c21y2) +
            $c13x3*(-2*$c20->getY()*$c21y2 - $c20y2*$c22->getY() - $c20->getY()*(2*$c20->getY()*$c22->getY() + $c21y2));

        $poly [] = -$c10->getX()*$c11->getX()*$c12->getY()*$c13->getX()*$c21->getY()*$c13->getY() + $c10->getX()*$c11->getY()*$c12->getX()*$c13->getX()*$c21->getY()*$c13->getY() + 6*$c10->getX()*$c11->getY()*$c21->getX()*$c12->getY()*$c13->getX()*$c13->getY() -
            6*$c10->getY()*$c11->getX()*$c12->getX()*$c13->getX()*$c21->getY()*$c13->getY() - $c10->getY()*$c11->getX()*$c21->getX()*$c12->getY()*$c13->getX()*$c13->getY() + $c10->getY()*$c11->getY()*$c12->getX()*$c21->getX()*$c13->getX()*$c13->getY() -
            $c11->getX()*$c11->getY()*$c12->getX()*$c21->getX()*$c12->getY()*$c13->getY() + $c11->getX()*$c11->getY()*$c12->getX()*$c12->getY()*$c13->getX()*$c21->getY() + $c11->getX()*$c20->getX()*$c12->getY()*$c13->getX()*$c21->getY()*$c13->getY() +
            6*$c11->getX()*$c12->getX()*$c20->getY()*$c13->getX()*$c21->getY()*$c13->getY() + $c11->getX()*$c20->getY()*$c21->getX()*$c12->getY()*$c13->getX()*$c13->getY() - $c20->getX()*$c11->getY()*$c12->getX()*$c13->getX()*$c21->getY()*$c13->getY() -
            6*$c20->getX()*$c11->getY()*$c21->getX()*$c12->getY()*$c13->getX()*$c13->getY() - $c11->getY()*$c12->getX()*$c20->getY()*$c21->getX()*$c13->getX()*$c13->getY() - 6*$c10->getX()*$c20->getX()*$c21->getX()*$c13y3 -
            2*$c10->getX()*$c21->getX()*$c12y3*$c13->getX() + 6*$c10->getY()*$c20->getY()*$c13x3*$c21->getY() + 2*$c20->getX()*$c21->getX()*$c12y3*$c13->getX() + 2*$c10->getY()*$c12x3*$c21->getY()*$c13->getY() -
            2*$c12x3*$c20->getY()*$c21->getY()*$c13->getY() - 6*$c10->getX()*$c10->getY()*$c21->getX()*$c13->getX()*$c13y2 + 3*$c10->getX()*$c11->getX()*$c12->getX()*$c21->getY()*$c13y2 -
            2*$c10->getX()*$c11->getX()*$c21->getX()*$c12->getY()*$c13y2 - 4*$c10->getX()*$c11->getY()*$c12->getX()*$c21->getX()*$c13y2 + 3*$c10->getY()*$c11->getX()*$c12->getX()*$c21->getX()*$c13y2 +
            6*$c10->getX()*$c10->getY()*$c13x2*$c21->getY()*$c13->getY() + 6*$c10->getX()*$c20->getX()*$c13->getX()*$c21->getY()*$c13y2 - 3*$c10->getX()*$c11->getY()*$c12->getY()*$c13x2*$c21->getY() +
            2*$c10->getX()*$c12->getX()*$c21->getX()*$c12y2*$c13->getY() + 2*$c10->getX()*$c12->getX()*$c12y2*$c13->getX()*$c21->getY() + 6*$c10->getX()*$c20->getY()*$c21->getX()*$c13->getX()*$c13y2 +
            4*$c10->getY()*$c11->getX()*$c12->getY()*$c13x2*$c21->getY() + 6*$c10->getY()*$c20->getX()*$c21->getX()*$c13->getX()*$c13y2 + 2*$c10->getY()*$c11->getY()*$c12->getX()*$c13x2*$c21->getY() -
            3*$c10->getY()*$c11->getY()*$c21->getX()*$c12->getY()*$c13x2 + 2*$c10->getY()*$c12->getX()*$c21->getX()*$c12y2*$c13->getX() - 3*$c11->getX()*$c20->getX()*$c12->getX()*$c21->getY()*$c13y2 +
            2*$c11->getX()*$c20->getX()*$c21->getX()*$c12->getY()*$c13y2 + $c11->getX()*$c11->getY()*$c21->getX()*$c12y2*$c13->getX() - 3*$c11->getX()*$c12->getX()*$c20->getY()*$c21->getX()*$c13y2 +
            4*$c20->getX()*$c11->getY()*$c12->getX()*$c21->getX()*$c13y2 - 6*$c10->getX()*$c20->getY()*$c13x2*$c21->getY()*$c13->getY() - 2*$c10->getX()*$c12x2*$c12->getY()*$c21->getY()*$c13->getY() -
            6*$c10->getY()*$c20->getX()*$c13x2*$c21->getY()*$c13->getY() - 6*$c10->getY()*$c20->getY()*$c21->getX()*$c13x2*$c13->getY() - 2*$c10->getY()*$c12x2*$c21->getX()*$c12->getY()*$c13->getY() -
            2*$c10->getY()*$c12x2*$c12->getY()*$c13->getX()*$c21->getY() - $c11->getX()*$c11->getY()*$c12x2*$c21->getY()*$c13->getY() - 4*$c11->getX()*$c20->getY()*$c12->getY()*$c13x2*$c21->getY() -
            2*$c11->getX()*$c11y2*$c21->getX()*$c13->getX()*$c13->getY() + 3*$c20->getX()*$c11->getY()*$c12->getY()*$c13x2*$c21->getY() - 2*$c20->getX()*$c12->getX()*$c21->getX()*$c12y2*$c13->getY() -
            2*$c20->getX()*$c12->getX()*$c12y2*$c13->getX()*$c21->getY() - 6*$c20->getX()*$c20->getY()*$c21->getX()*$c13->getX()*$c13y2 - 2*$c11->getY()*$c12->getX()*$c20->getY()*$c13x2*$c21->getY() +
            3*$c11->getY()*$c20->getY()*$c21->getX()*$c12->getY()*$c13x2 - 2*$c12->getX()*$c20->getY()*$c21->getX()*$c12y2*$c13->getX() - $c11y2*$c12->getX()*$c21->getX()*$c12->getY()*$c13->getX() +
            6*$c20->getX()*$c20->getY()*$c13x2*$c21->getY()*$c13->getY() + 2*$c20->getX()*$c12x2*$c12->getY()*$c21->getY()*$c13->getY() + 2*$c11x2*$c11->getY()*$c13->getX()*$c21->getY()*$c13->getY() +
            $c11x2*$c12->getX()*$c12->getY()*$c21->getY()*$c13->getY() + 2*$c12x2*$c20->getY()*$c21->getX()*$c12->getY()*$c13->getY() + 2*$c12x2*$c20->getY()*$c12->getY()*$c13->getX()*$c21->getY() +
            3*$c10x2*$c21->getX()*$c13y3 - 3*$c10y2*$c13x3*$c21->getY() + 3*$c20x2*$c21->getX()*$c13y3 + $c11y3*$c21->getX()*$c13x2 - $c11x3*$c21->getY()*$c13y2 -
            3*$c20y2*$c13x3*$c21->getY() - $c11->getX()*$c11y2*$c13x2*$c21->getY() + $c11x2*$c11->getY()*$c21->getX()*$c13y2 - 3*$c10x2*$c13->getX()*$c21->getY()*$c13y2 +
            3*$c10y2*$c21->getX()*$c13x2*$c13->getY() - $c11x2*$c12y2*$c13->getX()*$c21->getY() + $c11y2*$c12x2*$c21->getX()*$c13->getY() - 3*$c20x2*$c13->getX()*$c21->getY()*$c13y2 +
            3*$c20y2*$c21->getX()*$c13x2*$c13->getY();

        $poly [] = $c10->getX()*$c10->getY()*$c11->getX()*$c12->getY()*$c13->getX()*$c13->getY() - $c10->getX()*$c10->getY()*$c11->getY()*$c12->getX()*$c13->getX()*$c13->getY() + $c10->getX()*$c11->getX()*$c11->getY()*$c12->getX()*$c12->getY()*$c13->getY() -
            $c10->getY()*$c11->getX()*$c11->getY()*$c12->getX()*$c12->getY()*$c13->getX() - $c10->getX()*$c11->getX()*$c20->getY()*$c12->getY()*$c13->getX()*$c13->getY() + 6*$c10->getX()*$c20->getX()*$c11->getY()*$c12->getY()*$c13->getX()*$c13->getY() +
            $c10->getX()*$c11->getY()*$c12->getX()*$c20->getY()*$c13->getX()*$c13->getY() - $c10->getY()*$c11->getX()*$c20->getX()*$c12->getY()*$c13->getX()*$c13->getY() - 6*$c10->getY()*$c11->getX()*$c12->getX()*$c20->getY()*$c13->getX()*$c13->getY() +
            $c10->getY()*$c20->getX()*$c11->getY()*$c12->getX()*$c13->getX()*$c13->getY() - $c11->getX()*$c20->getX()*$c11->getY()*$c12->getX()*$c12->getY()*$c13->getY() + $c11->getX()*$c11->getY()*$c12->getX()*$c20->getY()*$c12->getY()*$c13->getX() +
            $c11->getX()*$c20->getX()*$c20->getY()*$c12->getY()*$c13->getX()*$c13->getY() - $c20->getX()*$c11->getY()*$c12->getX()*$c20->getY()*$c13->getX()*$c13->getY() - 2*$c10->getX()*$c20->getX()*$c12y3*$c13->getX() +
            2*$c10->getY()*$c12x3*$c20->getY()*$c13->getY() - 3*$c10->getX()*$c10->getY()*$c11->getX()*$c12->getX()*$c13y2 - 6*$c10->getX()*$c10->getY()*$c20->getX()*$c13->getX()*$c13y2 +
            3*$c10->getX()*$c10->getY()*$c11->getY()*$c12->getY()*$c13x2 - 2*$c10->getX()*$c10->getY()*$c12->getX()*$c12y2*$c13->getX() - 2*$c10->getX()*$c11->getX()*$c20->getX()*$c12->getY()*$c13y2 -
            $c10->getX()*$c11->getX()*$c11->getY()*$c12y2*$c13->getX() + 3*$c10->getX()*$c11->getX()*$c12->getX()*$c20->getY()*$c13y2 - 4*$c10->getX()*$c20->getX()*$c11->getY()*$c12->getX()*$c13y2 +
            3*$c10->getY()*$c11->getX()*$c20->getX()*$c12->getX()*$c13y2 + 6*$c10->getX()*$c10->getY()*$c20->getY()*$c13x2*$c13->getY() + 2*$c10->getX()*$c10->getY()*$c12x2*$c12->getY()*$c13->getY() +
            2*$c10->getX()*$c11->getX()*$c11y2*$c13->getX()*$c13->getY() + 2*$c10->getX()*$c20->getX()*$c12->getX()*$c12y2*$c13->getY() + 6*$c10->getX()*$c20->getX()*$c20->getY()*$c13->getX()*$c13y2 -
            3*$c10->getX()*$c11->getY()*$c20->getY()*$c12->getY()*$c13x2 + 2*$c10->getX()*$c12->getX()*$c20->getY()*$c12y2*$c13->getX() + $c10->getX()*$c11y2*$c12->getX()*$c12->getY()*$c13->getX() +
            $c10->getY()*$c11->getX()*$c11->getY()*$c12x2*$c13->getY() + 4*$c10->getY()*$c11->getX()*$c20->getY()*$c12->getY()*$c13x2 - 3*$c10->getY()*$c20->getX()*$c11->getY()*$c12->getY()*$c13x2 +
            2*$c10->getY()*$c20->getX()*$c12->getX()*$c12y2*$c13->getX() + 2*$c10->getY()*$c11->getY()*$c12->getX()*$c20->getY()*$c13x2 + $c11->getX()*$c20->getX()*$c11->getY()*$c12y2*$c13->getX() -
            3*$c11->getX()*$c20->getX()*$c12->getX()*$c20->getY()*$c13y2 - 2*$c10->getX()*$c12x2*$c20->getY()*$c12->getY()*$c13->getY() - 6*$c10->getY()*$c20->getX()*$c20->getY()*$c13x2*$c13->getY() -
            2*$c10->getY()*$c20->getX()*$c12x2*$c12->getY()*$c13->getY() - 2*$c10->getY()*$c11x2*$c11->getY()*$c13->getX()*$c13->getY() - $c10->getY()*$c11x2*$c12->getX()*$c12->getY()*$c13->getY() -
            2*$c10->getY()*$c12x2*$c20->getY()*$c12->getY()*$c13->getX() - 2*$c11->getX()*$c20->getX()*$c11y2*$c13->getX()*$c13->getY() - $c11->getX()*$c11->getY()*$c12x2*$c20->getY()*$c13->getY() +
            3*$c20->getX()*$c11->getY()*$c20->getY()*$c12->getY()*$c13x2 - 2*$c20->getX()*$c12->getX()*$c20->getY()*$c12y2*$c13->getX() - $c20->getX()*$c11y2*$c12->getX()*$c12->getY()*$c13->getX() +
            3*$c10y2*$c11->getX()*$c12->getX()*$c13->getX()*$c13->getY() + 3*$c11->getX()*$c12->getX()*$c20y2*$c13->getX()*$c13->getY() + 2*$c20->getX()*$c12x2*$c20->getY()*$c12->getY()*$c13->getY() -
            3*$c10x2*$c11->getY()*$c12->getY()*$c13->getX()*$c13->getY() + 2*$c11x2*$c11->getY()*$c20->getY()*$c13->getX()*$c13->getY() + $c11x2*$c12->getX()*$c20->getY()*$c12->getY()*$c13->getY() -
            3*$c20x2*$c11->getY()*$c12->getY()*$c13->getX()*$c13->getY() - $c10x3*$c13y3 + $c10y3*$c13x3 + $c20x3*$c13y3 - $c20y3*$c13x3 -
            3*$c10->getX()*$c20x2*$c13y3 - $c10->getX()*$c11y3*$c13x2 + 3*$c10x2*$c20->getX()*$c13y3 + $c10->getY()*$c11x3*$c13y2 +
            3*$c10->getY()*$c20y2*$c13x3 + $c20->getX()*$c11y3*$c13x2 + $c10x2*$c12y3*$c13->getX() - 3*$c10y2*$c20->getY()*$c13x3 - $c10y2*$c12x3*$c13->getY() +
            $c20x2*$c12y3*$c13->getX() - $c11x3*$c20->getY()*$c13y2 - $c12x3*$c20y2*$c13->getY() - $c10->getX()*$c11x2*$c11->getY()*$c13y2 +
            $c10->getY()*$c11->getX()*$c11y2*$c13x2 - 3*$c10->getX()*$c10y2*$c13x2*$c13->getY() - $c10->getX()*$c11y2*$c12x2*$c13->getY() + $c10->getY()*$c11x2*$c12y2*$c13->getX() -
            $c11->getX()*$c11y2*$c20->getY()*$c13x2 + 3*$c10x2*$c10->getY()*$c13->getX()*$c13y2 + $c10x2*$c11->getX()*$c12->getY()*$c13y2 +
            2*$c10x2*$c11->getY()*$c12->getX()*$c13y2 - 2*$c10y2*$c11->getX()*$c12->getY()*$c13x2 - $c10y2*$c11->getY()*$c12->getX()*$c13x2 + $c11x2*$c20->getX()*$c11->getY()*$c13y2 -
            3*$c10->getX()*$c20y2*$c13x2*$c13->getY() + 3*$c10->getY()*$c20x2*$c13->getX()*$c13y2 + $c11->getX()*$c20x2*$c12->getY()*$c13y2 - 2*$c11->getX()*$c20y2*$c12->getY()*$c13x2 +
            $c20->getX()*$c11y2*$c12x2*$c13->getY() - $c11->getY()*$c12->getX()*$c20y2*$c13x2 - $c10x2*$c12->getX()*$c12y2*$c13->getY() - 3*$c10x2*$c20->getY()*$c13->getX()*$c13y2 +
            3*$c10y2*$c20->getX()*$c13x2*$c13->getY() + $c10y2*$c12x2*$c12->getY()*$c13->getX() - $c11x2*$c20->getY()*$c12y2*$c13->getX() + 2*$c20x2*$c11->getY()*$c12->getX()*$c13y2 +
            3*$c20->getX()*$c20y2*$c13x2*$c13->getY() - $c20x2*$c12->getX()*$c12y2*$c13->getY() - 3*$c20x2*$c20->getY()*$c13->getX()*$c13y2 + $c12x2*$c20y2*$c12->getY()*$c13->getX();



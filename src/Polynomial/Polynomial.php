<?php
/** Freesewing\Polynomial class */
namespace Freesewing;

/**
 * Polynomial operations.
 *
 * This class is only used to help us determine the
 * intersections between two cubic Bezier curves.
 *
 * @author    Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license   http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Polynomial
{

    /** * @var float $precision Ploynomial precision */
    public $precision = 6;

    /** * @var float $tolerance Ploynomial tolerance */
    public $tolerance = 0.000001;

    /** * @var array $coefs Coeficients */
    public $coefs = array();

    /**
     * Constructor
     *
     * @param array $coefs The coeficients
     */
    public function __construct($coefs)
    {
        for ($i = count($coefs) - 1; $i >= 0; $i--) {
            array_push($this->coefs, $coefs[$i]);
        }
        $this->_variable = "t";
        $this->_s = 0;
    }

    /**
     * Pushes a coeficient onto the coefs array
     *
     * @param scalar $value The coeficient to add
     */
    public function addCoef($value)
    {
        array_push($this->coefs, $value);
    }

    /**
     * Returns a coeficient from the coefs array
     *
     * @param int $index The index in the coefs array
     */
    public function getCoef($index)
    {
        return $this->coefs[$index];
    }

    /**
     * Returns the degree of a polynomial
     *
     * @return int $degree The degree of the polynomial
     * @todo Remove this, it's not used
     */
    public function getDegree()
    {
        return count($this->coefs) - 1;
    }

    /**
     * Returns the derivative of a polymomial
     *
     * @return \Freesewing\Polynomial $d The derivative
     */
    public function getDerivative()
    {
        $d = new Polynomial(array());
        for ($i = 1; $i < count($this->coefs); $i++) {
            $d->addCoef($i * $this->getCoef($i));
        }

        return $d;
    }

    /**
     * Evaluates a polynomial
     *
     * @param float $x The input
     */
    public function evalu($x)
    {
        if (!is_numeric($x)) {
            throw new \InvalidArgumentException("Polinomial::Eval() : Parameter must be numeric");
        }

        $result = 0;
        for ($i = count($this->coefs) - 1; $i >= 0; $i--) {
            $result = $result * $x + $this->coefs[$i];
        }
        return $result;
    }

    /**
     * Polynomial bisection
     *
     * @param float $min Lower edge of interval
     * @param float $max Upper edge of interval
     *
     * @return float $result The result
     */
    public function bisection($min, $max)
    {
        $minValue = $this->evalu($min);
        $maxValue = $this->evalu($max);

        if (abs($minValue) <= $this->tolerance) {
            $result = $min;
        } elseif (abs($maxValue) <= $this->tolerance) {
            $result = $max;
        } elseif ($minValue * $maxValue <= 0) {
            $tmp1 = log($max - $min);
            $tmp2 = log(10) * $this->precision;
            $iters = ceil(($tmp1 + $tmp2) / log(2));

            for ($i = 0; $i < $iters; $i++) {
                $result = 0.5 * ($min + $max);
                $value = $this->evalu($result);

                if (abs($value) <= $this->tolerance) {
                    break;
                }

                if ($value * $minValue < 0) {
                    $max = $result;
                    $maxValue = $value;
                } else {
                    $min = $result;
                    $minValue = $value;
                }
            }
        }

        return @$result;
    }

    /**
     * Finds polynomial roots within interval
     *
     * @param float $min Lower edge of interval
     * @param float $max Upper edge of interval
     */
    public function getRootsInInterval($min = 0, $max = 1)
    {
        $roots = array();
        if ($this->getDegree() == 1) {
            $root = $this->bisection($min, $max);
            if ($root != null) {
                $roots[] = $root;
            }
        } else {
            // get roots of derivative
            $deriv = $this->getDerivative();
            $droots = $deriv->getRootsInInterval($min, $max);
            if (count($droots) > 0) {
                // find root on [min, droots[0]]
                $root = $this->bisection($min, $droots[0]);
                if ($root != null) {
                    $roots[] = $root;
                }

                // find root on [droots[i],droots[i+1]] for 0 <= i <= count-2
                for ($i = 0; $i <= count($droots) - 2; $i++) {
                    $root = $this->bisection($droots[$i], $droots[$i + 1]);
                    if ($root != null) {
                        $roots[] = $root;
                    }
                }

                // find root on [droots[count-1],xmax]
                $root = $this->bisection($droots[count($droots) - 1], $max);
                if ($root != null) {
                    $roots[] = $root;
                }
            } else {
                // polynomial is monotone on [min,max], has at most one root
                $root = $this->bisection($min, $max);
                if ($root != null) {
                    $roots[] = $root;
                }
            }
        }
        return $roots;
    }

    /**
     * Roots, bloody roots
     */
    public function getRoots()
    {
        $this->simplify();
        switch ($this->getDegree()) {
            case 0:
                $result = array();
                break;
            case 1:
                $result = $this->getLinearRoot();
                break;
            case 2:
                $result = $this->getQuadraticRoots();
                break;
            case 3:
                $result = $this->getCubicRoots();
                break;
            case 4:
                $result = $this->getQuarticRoots();
                break;
            default:
                $result = array(); // Not implemented
        }

        return $result;
    }

    /**
     * Simplifies a polynomial, if possible
     */
    public function simplify()
    {
        for ($i = $this->getDegree(); $i >= 0; $i--) {
            if (abs($this->coefs[$i]) <= $this->tolerance) {
                array_pop($this->coefs);
            } else {
                break;
            }
        }
    }

    /**
     * Gets roots of a cubic polynomial
     *
     * @return array $results Array of roots
     */
    public function getCubicRoots()
    {
        $results = array();

        if ($this->getDegree() == 3) {
            $c3 = $this->coefs[3];
            $c2 = $this->coefs[2] / $c3;
            $c1 = $this->coefs[1] / $c3;
            $c0 = $this->coefs[0] / $c3;

            $a = (3 * $c1 - $c2 * $c2) / 3;
            $b = (2 * $c2 * $c2 * $c2 - 9 * $c1 * $c2 + 27 * $c0) / 27;
            $offset = $c2 / 3;
            $discrim = $b * $b / 4 + $a * $a * $a / 27;
            $halfB = $b / 2;

            if (abs($discrim) <= $this->tolerance) {
                $disrim = 0;
            }

            if ($discrim > 0) {
                $e = sqrt($discrim);

                $tmp = -1 * $halfB + $e;
                if ($tmp >= 0) {
                    $root = pow($tmp, 1 / 3);
                } else {
                    $root = -1 * pow(-1 * $tmp, 1 / 3);
                }

                $tmp = -1 * $halfB - $e;
                if ($tmp >= 0) {
                    $root += pow($tmp, 1 / 3);
                } else {
                    $root -= pow(-1 * $tmp, 1 / 3);
                }

                $results[] = $root - $offset;
            } elseif ($discrim < 0) {
                $distance = sqrt(-1 * $a / 3);
                $angle = atan2(sqrt(-1 * $discrim), -1 * $halfB) / 3;
                $cos = cos($angle);
                $sin = sin($angle);
                $sqrt3 = sqrt(3);

                $results[] = 2 * $distance * $cos - $offset;
                $results[] = -1 * $distance * ($cos + $sqrt3 * $sin) - $offset;
                $results[] = -1 * $distance * ($cos - $sqrt3 * $sin) - $offset;

            } else {
                if ($halfB >= 0) {
                    $tmp = -1 * pow($halfB, 1 / 3);
                } else {
                    $tmp = pow(-1 * $halfB, 1 / 3);
                }

                $results[] = 2 * $tmp - $offset;
                // really should return next root twice, but we return only one
                $results[] = -1 * $tmp - $offset;
            }
        }

        return $results;
    }

    /**
     * Calculate Roots by using the pq-formular.
     *
     * @return array
     */
    public function getQuadraticRoots()
    {
        $results = [];

        if ($this->getDegree() == 2) {
            $c2 = $this->coefs[2];
            $c1 = $this->coefs[1];
            $c0 = $this->coefs[0];

            // get p and q thru division into normal form
            $p = $c1 / $c2;
            $q = $c0 / $c2;

            // helping values
            $c = $p / 2;
            $cNegativ = $c * -1;
            $d = ($c * $c) - $q;

            // there is a irrational solution ... but no answer for us
            if ($d < 0) {
                // no solution
            }

            // there are only one answer
            if ($d == 0) {
                $x1 = $cNegativ;

                $results[] = $x1;
                $results[] = $x1;
            }

            // there are two answers
            if ($d > 0) {
                $z = sqrt($d);
                $x1 = $cNegativ + $z;
                $x2 = $cNegativ - $z;

                $results[] = $x1;
                $results[] = $x2;
            }
        }

        return $results;
    }
}

---
layout: class
title: BezierToolbox
tags: [bezierCurves, internal, class, namespaceFreesewing]
permalink: /class/BezierToolbox
---
<div class="col-xs-12 col-md-3 toc" markdown="1">
## Contents
{:.no_toc}
* TOC - Do not remove this line
{:toc}
</div>
<div class="col-xs-12 col-md-9" markdown="1">
## Description 

The [`BezierToolbox`](BezierToolbox) class provides a number of static utility methods to 
help with handling Bezier curves.

> As all of the public class methods are static, there is no need to 
instantiate an object of this class to use them.

## Typical use

The [`BezierToolbox`](BezierToolbox) class is internal. Most of its methods are only
ever called from the [`Path`](Path) and [`Part`](Part) classes. 

An exception is the [`BezierToolbox::bezierCircle`](BezierToolbox#beziercircle) method
which is commonly called from a [`Pattern`](Pattern)

## Constants

### STEPS

`STEPS` sets the number of steps when walking a path. 
In other words, methods that chop a path into tiny pieces to get something 
done will chop it into `STEPS` pieces.

`STEPS` is an `integer` with a value of `100`.

## Public methods

### bezierCircle

```php?start_inline=1
float bezierCircle( 
    float $radius 
)
```
Returns `$radius * 4 * (sqrt(2) - 1) / 3` which is the radius that mimics a quarter circle
segment in a Bezier Curve.

You can't make a perfect circle with a cubic Bezier curve, but you can come close by
using by using the value returned by this method to offset your control points.

Delta `$t` is a value between 0 and 1 that indicates how far into 
the curve we need to split. It is typically the result of an earlier call
to [`BezierToolbox::bezierDelta`](BezierToolbox#bezierdelta)

#### Example
{:.no_toc}

{% include classTabs.html
    id="bezierCircle" 
%}

<div class="tab-content">
<div role="tabpanel" class="tab-pane active" id="bezierCircle-result">

{% include figure.html 
    description="Make your Bezier curve mimic a smooth circle segment"
    url="https://api.freesewing.org/?service=draft&pattern=ClassDocs&theme=Designer&onlyPoints=1,2,3,4,5,6&class=BezierToolbox&method=bezierCircle"
%}

</div>
<div role="tabpanel" class="tab-pane" id="bezierCircle-code" markdown="1">

```php?start_inline=1
$r = \Freesewing\BezierToolbox::bezierCircle(50);

/** @var \Freesewing\Part $p */
$p->newPoint(1, 0, 0);
$p->newPoint(2, 50, 0);
$p->newPoint(3, 50+$r, 0);
$p->newPoint(4, 100, 50-$r);
$p->newPoint(5, 100, 50);
$p->newPoint(6, 100,100);

$p->newPath(1,"M 1 L 2 C 3 4 5 L 6");
```

</div>
</div>

#### Typical use
{:.no_toc}

Only called from patterns to get a radius that mimics a circle.

> While you can call this directly from your pattern, we recommend using the
> [`Part::bezierCircle`](Part#beziercircle) method, which wraps this one.

#### Parameters
{:.no_toc}

This expects a float, which is the radius of the circle you want to mimic.

#### Return value
{:.no_toc}

Returns a float that indicates how far the offset your control points to mimic a circular
bend over 90 degrees.

### bezierLength

```php?start_inline=1
float bezierLength( 
    Freesewing\Point $start, 
    Freesewing\Point $cp1, 
    Freesewing\Point $cp2, 
    Freesewing\Point $end 
)
```

Calculates the length of a cubic Bezier curve.

> Note that calculating the length of a Bezier Curve is tricky.
> This chops the Bezier Curve in `STEPS` straight line segments to
> approximate the lenght of the curve.


#### Example
{:.no_toc}

{% include classTabs.html
    id="bezierLength" 
%}

<div class="tab-content">
<div role="tabpanel" class="tab-pane active" id="bezierLength-result">

{% include figure.html 
    description="A bezierEdge example"
    url="https://api.freesewing.org/?service=draft&pattern=ClassDocs&theme=Designer&onlyPoints=1,2,3,4&class=BezierToolbox&method=bezierLength"
%}

</div>
<div role="tabpanel" class="tab-pane" id="bezierLength-code" markdown="1">

```php?start_inline=1
/** @var \Freesewing\Part $p */
$p->newPoint(1, 0, 100);
$p->newPoint(2, 30, 0);
$p->newPoint(3, 100, 100);
$p->newPoint(4, 100, 50);

$p->newPath(1,"M 1 C 2 3 4");   

$p->newTextOnPath(1,"M 1 C 2 3 4", "Length of this curve: ".$this->unit($p->curveLen(1,2,3,4)), ["dy" => -2,'class' => 'text-center']);
```

</div>
</div>

#### Typical use
{:.no_toc}

Only used through [`Part::curveLen`](Part#curvelen) 
which allows you to pass [`Point`](Point) IDs from the [`Part`](Part)'s points array, 
rather than the actual [`Point`](Point) objects.

#### Parameters
{:.no_toc}

This expects four [`Point`](Point) objects that describe the Bezier curve.

#### Return value
{:.no_toc}

Returns a `float` that is (approximately) the length of the Bezier Curve.

### bezierEdge

```php?start_inline=1
\Freesewing\Point bezierEdge( 
    Freesewing\Point $start, 
    Freesewing\Point $cp1, 
    Freesewing\Point $cp2, 
    Freesewing\Point $end 
    string $direction = 'left'
)
```

This finds the point on the edge of a Bezier curve by walking through the
curve and keeping an eye on its outermost points.

The side is either `left`, `right`, `up` or `down`.

#### Example
{:.no_toc}

{% include classTabs.html
    id="bezierEdge" 
%}

<div class="tab-content">
<div role="tabpanel" class="tab-pane active" id="bezierEdge-result">

{% include figure.html 
    description="A bezierEdge example"
    url="https://api.freesewing.org/?service=draft&pattern=ClassDocs&theme=Designer&onlyPoints=1,2,3,4,leftEdge,rightEdge,topEdge,bottomEdge&class=BezierToolbox&method=bezierEdge"
%}

</div>
<div role="tabpanel" class="tab-pane" id="bezierEdge-code" markdown="1">

```php?start_inline=1
/** @var \Freesewing\Part $p */
$p->newPoint(1, 50, 50);
$p->newPoint(2, 0, 0);
$p->newPoint(3, 230, 120);
$p->newPoint(4, 100, 100);

$p->newPath(1,"M 1 C 2 3 4");   

$p->addPoint('leftEdge', $p->curveEdge(1,2,3,4,'left'));
$p->addPoint('rightEdge', $p->curveEdge(1,2,3,4,'right'));
$p->addPoint('topEdge', $p->curveEdge(1,2,3,4,'top'));
$p->addPoint('bottomEdge', $p->curveEdge(1,2,3,4,'bottom'));

$p->newTextOnPath(1,"M 1 C 2 3 4", "Bezier curve", ["dy" => -2]);
$p->newNote(1,"leftEdge", "Left edge", 9, 15, 0);
$p->newNote(2,"rightEdge", "Right edge", 3, 15, 0);
$p->newNote(3,"topEdge", "Top edge", 12, 15, 0);
$p->newNote(4,"bottomEdge", "Bottom edge", 6, 15, 0);
```

</div>
</div>

#### Typical use
{:.no_toc}

Only used through [`Part::curveEdge`](Part#curveedge) 
which allows you to pass [`Point`](Point) IDs from the [`Part`](Part)'s points array, 
rather than the actual [`Point`](Point) objects.

#### Parameters
{:.no_toc}

This expects four [`Point`](Point) objects that describe the Bezier curve and an
additional fifth parameter to indicate direction.

Direction is a string and should be one of these:

- `top`
- `bottom`
- `left`
- `right`

Direction is optional, if ommited it defaults to `left`.

#### Return value
{:.no_toc}

It returns a [`Point`](Point) object that sits at the chosen edge of the Bezier curve.

### bezierBoundary

```php?start_inline=1
\Freesewing\Boundary bezierBoundary( 
    Freesewing\Point $start, 
    Freesewing\Point $cp1, 
    Freesewing\Point $cp2, 
    Freesewing\Point $end 
)
```

This calculates the bounding box by walking through a Bezier curve while 
keeping an eye on the coordinates and registering the most topLeft and 
boZZttomRight point we encounter.

#### Example
{:.no_toc}

{% include classTabs.html
    id="bezierBoundary" 
%}

<div class="tab-content">
<div role="tabpanel" class="tab-pane active" id="bezierBoundary-result">

{% include figure.html 
    description="A bezierBoundary example"
    url="https://api.freesewing.org/?service=draft&pattern=ClassDocs&theme=Designer&onlyPoints=1,2,3,4&class=BezierToolbox&method=bezierBoundary" 
%}

</div>
<div role="tabpanel" class="tab-pane" id="bezierBoundary-code" markdown="1">

```php?start_inline=1
/** @var \Freesewing\Part $p */
$p->newPoint(1, 50, 50);
$p->newPoint(2, 0, 0);
$p->newPoint(3, 230, 120);
$p->newPoint(4, 100, 100);

$p->newPath(1,'M 1 C 2 3 4');   
$boundary = $p->paths[1]->findBoundary($p);

$p->addPoint('topLeft', $boundary->getTopLeft());
$p->addPoint('bottomRight', $boundary->getBottomRight());
$p->newPoint('topRight', $p->x('bottomRight'), $p->y('topLeft'));
$p->newPoint('bottomLeft', $p->x('topLeft'), $p->y('bottomRight'));

$p->newPath(2,'M topLeft L topRight L bottomRight L bottomLeft z', ['class' => 'helpline']);   
$p->newTextOnPath(1,'M 1 C 2 3 4', 'Bezier curve', ['dy' => -2]);
$p->newNote(1,'topRight', 'Boundary', 2, 15, 0);
```

</div>
</div>

#### Typical use
{:.no_toc}

Only used in [`Path::findBoundary`](Path#findboundary).

#### Parameters
{:.no_toc}

This expects four [`Point`](Point) objects that describe the Bezier curve.

#### Return value
{:.no_toc}

It returns a [`Boundary`](Boundary) object that describes the bounding box.


### bezierDelta

```php?start_inline=1
float bezierDelta( 
    Freesewing\Point $from, 
    Freesewing\Point $cp1, 
    Freesewing\Point $cp2, 
    Freesewing\Point $to 
    Freesewing\Point $split 
)
```
Finds the delta (between 0 and 1) of [`Point`](Point) `$split`
on a Bezier curve.

For example, if `$split` is exactly halfway the curve, this will
return 0.5.

#### Example
{:.no_toc}

{% include classTabs.html
    id="bezierDelta" 
%}

<div class="tab-content">
<div role="tabpanel" class="tab-pane active" id="bezierDelta-result">

{% include figure.html 
    description="Delta of a point along a Bezier curve"
    url="https://api.freesewing.org/?service=draft&pattern=ClassDocs&theme=Designer&onlyPoints=1,2,3,4,5&class=BezierToolbox&method=bezierDelta"
%}

</div>
<div role="tabpanel" class="tab-pane" id="bezierDelta-code" markdown="1">

```php?start_inline=1
/** @var \Freesewing\Part $p */
$p->newPoint(1, 0, 100);
$p->newPoint(2, 30, 0);
$p->newPoint(3, 100, 100);
$p->newPoint(4, 100, 50);
$p->addPoint(5, $p->shiftAlong(1,2,3,4, 50));

$p->newPath(1,"M 1 C 2 3 4");

$delta = \Freesewing\BezierToolbox::bezierDelta(
    $p->loadPoint(1),
    $p->loadPoint(2),
    $p->loadPoint(3),
    $p->loadPoint(4),
    $p->loadPoint(5)
);
$p->newNote(1,5, "Delta of this point: $delta", 5, 25, 0, ['dy' => 2]);
```

</div>
</div>

#### Typical use
{:.no_toc}

Used only in [`Part::splitCurve`](Part#splitcurve). 

#### Parameters
{:.no_toc}

This expects four [`Point`](Point) objects that describe the Bezier curve, followed 
by a fifth [`Point`](Point) object which is the point on the curve for which
to calculate the delta.

#### Return value
{:.no_toc}

Returns a `float` between 0 and 1, indicating the position along the curve
of the [`Point`](Point) `$split`

### bezierSplit

```php?start_inline=1
array bezierSplit( 
    Freesewing\Point $from, 
    Freesewing\Point $cp1, 
    Freesewing\Point $cp2, 
    Freesewing\Point $to 
    float $t 
)
```
Splits a cubic Bezier curve at delta `$t`, and calculates the control
and end points for a cubic Bezier from `$from` to the [`Point`](Point)
at `$t`.

Delta `$t` is a value between 0 and 1 that indicates how far into 
the curve we need to split. It is typically the result of an earlier call
to [`BezierToolbox::bezierDelta`](BezierToolbox#bezierdelta)

#### Example
{:.no_toc}

{% include classTabs.html
    id="bezierSplit" 
%}

<div class="tab-content">
<div role="tabpanel" class="tab-pane active" id="bezierSplit-result">

{% include figure.html 
    description="The methods calculates new points that allow us to construct a curve that is a subsection of another curve"
    url="https://api.freesewing.org/?service=draft&pattern=ClassDocs&theme=Designer&onlyPoints=1,2,3,4,5,s5,s6,s7&class=BezierToolbox&method=bezierSplit"
%}

</div>
<div role="tabpanel" class="tab-pane" id="bezierSplit-code" markdown="1">

```php?start_inline=1
/** @var \Freesewing\Part $p */
$p->newPoint(1, 0, 100);
$p->newPoint(2, 30, 0);
$p->newPoint(3, 100, 100);
$p->newPoint(4, 100, 50);
$p->addPoint(5, $p->shiftAlong(1,2,3,4, 50));

$p->newPath(1,"M 1 C 2 3 4");

// This will add points 's1' to 's8' 
// to the part's points array
$p->addSplitCurve(1,2,3,4,5,'s');

$p->newPath(2,"M s5 C s6 s7 s8", ['class' => 'debug']);
```

</div>
</div>

#### Typical use
{:.no_toc}

Used only in [`Part::splitCurve`](Part#splitcurve). 

#### Parameters
{:.no_toc}

This expects four [`Point`](Point) objects that describe the Bezier curve,
and an additional 5th parameter for the delta `$t`, between 0 and 1.

#### Return value
{:.no_toc}

Returns an array with 4 [`Point`](Point) objects that describe the Bezier
curve from `$start` to the point at delta `$t`. More precisely:

- The original `$from` [`Point`](Point)
- A [`Point`](Point) that is the control point of the splitted curve on the `$from` side
- A [`Point`](Point) that is the control point of the splitted curve on the `$to` side
- The [`Point`](Point) that sits at delta `$t` in the curve and is the end of the splitted curve

### bezierLineIntersections

```php?start_inline=1
array|false bezierLineIntersections( 
    Freesewing\Point $lFrom, 
    Freesewing\Point $lTo 
    Freesewing\Point $cFrom, 
    Freesewing\Point $cC1, 
    Freesewing\Point $cC2, 
    Freesewing\Point $cTo 
)
```

Finds the intersection(s) between a line segment and a cubic Bezier Curve.

#### Example
{:.no_toc}

{% include classTabs.html
    id="bezierLineIntersections" 
%}

<div class="tab-content">
<div role="tabpanel" class="tab-pane active" id="bezierLineIntersections-result">

{% include figure.html 
    description="This curve and line intersect in three points"
    url="https://api.freesewing.org/?service=draft&pattern=ClassDocs&theme=Designer&onlyPoints=1,2,3,4,i1,i2,i3&class=BezierToolbox&method=bezierLineIntersections"
%}

</div>
<div role="tabpanel" class="tab-pane" id="bezierLineIntersections-code" markdown="1">

```php?start_inline=1
/** @var \Freesewing\Part $p */
$p->newPoint(1, 0, 100);
$p->newPoint(2, 30, 0);
$p->newPoint(3, 100, 100);
$p->newPoint(4, 100, 50);

$p->newPoint(5, 0, 80);
$p->newPoint(6, 110, 55);

$p->newPath(1,"M 1 C 2 3 4 M 5 L 6");

// This will add points 'i1', 'i2' and 'i3' 
// to the part's points array
$p->curveCrossesLine(1,2,3,4,5,6,'i');
```

</div>
</div>

#### Typical use
{:.no_toc}

Typically used via [`Part::curveCrossesLine`](Part#curvecrossesline) 
which allows you to pass [`Point`](Point) IDs from the [`Part`](Part)'s points array, 
rather than the actual [`Point`](Point) objects.

#### Parameters
{:.no_toc}

This expects two [`Point`](Point) object that are the start and endpoint of 
the line segment, followed by four [`Point`](Point) objects that describe 
the Bezier curve.

#### Return value
{:.no_toc}

Returns either `false` when no intersections are found. Or an array of 
[`Point`](Point) objects with the intersection point(s).

### bezierBezierIntersections

```php?start_inline=1
array|false bezierBezierIntersections( 
    Freesewing\Point $c1From, 
    Freesewing\Point $c1C1 
    Freesewing\Point $c1C2, 
    Freesewing\Point $c1To, 
    Freesewing\Point $c2From, 
    Freesewing\Point $c2C1 
    Freesewing\Point $c2C2, 
    Freesewing\Point $c2To, 
)
```

Finds the intersection(s) between 2 cubic Bezier Curves.

#### Example
{:.no_toc}

{% include classTabs.html
    id="bezierBezierIntersections" 
%}

<div class="tab-content">
<div role="tabpanel" class="tab-pane active" id="bezierBezierIntersections-result">

{% include figure.html 
    description="Two cubic Bezier curves can intersect in up to nine points"
    url="https://api.freesewing.org/?service=draft&pattern=ClassDocs&theme=Designer&onlyPoints=i-1,i-2,i-3,i-4,i-5,i-6,i-7,i-8,i-9&class=BezierToolbox&method=bezierBezierIntersections"
%}

</div>
<div role="tabpanel" class="tab-pane" id="bezierBezierIntersections-code" markdown="1">

```php?start_inline=1
/** @var \Freesewing\Part $p */
$p->newPoint(1, 0, 100);
$p->newPoint(2, 0, -200);
$p->newPoint(3, 100, 300);
$p->newPoint(4, 100, 0);
$p->newPath(1,"M 1 C 2 3 4");

$p->newPoint(5, 0, 10);
$p->newPoint(6, 330, 10);
$p->newPoint(7, -230, 90);
$p->newPoint(8, 100, 90);
$p->newPath(2,"M 5 C 6 7 8");

// This will add points 'i1' => 'i9' 
// to the part's points array
$p->curvesCross(1,2,3,4,5,6,7,8,'i');
```

</div>
</div>

#### Typical use
{:.no_toc}

Typically used via [`Part::curveCrossesLine`](Part#curvecrossesline) 
which allows you to pass [`Point`](Point) IDs from the [`Part`](Part)'s points array, 
rather than the actual [`Point`](Point) objects.

#### Parameters
{:.no_toc}

This expects eight [`Point`](Point) objects. The first 4 describe the first
Bezier curve, and the last four describe the second Bezier curve.

#### Return value
{:.no_toc}

Returns either `false` when no intersections are found. Or an array of 
[`Point`](Point) objects with the intersection point(s).

{% include classFooter.html %}
</div>


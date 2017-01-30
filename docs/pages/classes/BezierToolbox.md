---
layout: class
title: BezierToolbox
namespace: Freesewing
tags: [bezierCurves, internal, class, namespaceFreesewing]
permalink: /class/BezierToolbox
---
* TOC 
{:toc}

## Description 

The [`BezierToolbox`](BezierToolbox) class provides a number of static utility methods to 
help with handling Bezier curves.

All of the class' public methods are static, so there is no need to 
instantiate an object of this class to use them.

## Typical use

The [`BezierToolbox`](BezierToolbox) class is internal. It provides helper methods to the 
[`Path`](Path) and [`Part`](Part) classes. 

## Constants

### STEPS

`STEPS` sets the number of steps when walking a path. 
In other words, methods that chop a path into tiny pieces to get something 
done will chop it into `STEPS` pieces.

`STEPS` is an `integer` with a value of `100`.

## Public methods

### findBezierBoundary

```php?start_inline=1
\Freesewing\Boundary findBezierBoundary( 
    Freesewing\Point $start, 
    Freesewing\Point $cp1, 
    Freesewing\Point $cp2, 
    Freesewing\Point $end 
)
```

This calculates the bounding box by walking through a Bezier curve while 
keeping an eye on the coordinates and registering the most topLeft and 
bottomRight point we encounter.

{% include tabs.html 
    nr="1" 
    description="A findBezierBoundary example"
    url="https://api.freesewing.org/?service=draft&pattern=ClassDocs&theme=Designer&onlyPoints=1,2,3,4" 
%}

#### Example
```php?start_inline=1
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
{% include figure.html 
    nr="1" 
    description="A findBezierBoundary example"
    url="https://api.freesewing.org/?service=draft&pattern=ClassDocs&theme=Designer&onlyPoints=1,2,3,4" 
%}

#### Typical use
{:.no_toc}

Only used in [`Path::findBoundary`](Path#findboundary).

#### Parameters
{:.no_toc}

This expects four [`Point`](Point) objects that describe the Bezier curve.

#### Return value
{:.no_toc}

It returns a [`Boundary`](Boundary) object that describes the bounding box.

### findBezierEdge

```php?start_inline=1
\Freesewing\Point findBezierEdge( 
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

It returns a [`Point`](Point) object that describes the bounding box.

### cubicBezierLength

```php?start_inline=1
float cubicBezierLength( 
    Freesewing\Point $start, 
    Freesewing\Point $cp1, 
    Freesewing\Point $cp2, 
    Freesewing\Point $end 
)
```

Calculates the length of a cubic Bezier curve.

Note that calculating the length of a Bezier Curve is tricky.
This chops the Bezier Curve in `STEPS` straight line segments to
approximate the lenght of the curve.

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

### findLineCurveIntersections

```php?start_inline=1
array|false cubicBezierLength( 
    Freesewing\Point $lFrom, 
    Freesewing\Point $lTo 
    Freesewing\Point $cFrom, 
    Freesewing\Point $cC1, 
    Freesewing\Point $cC2, 
    Freesewing\Point $cTo 
)
```

Finds the intersection(s) between a line segment and a cubic Bezier Curve.

#### Typical use
{:.no_toc}

Used through [`Part::curveCrossesLine`](Part#curvecrossesline) 
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

### cubicBezierDelta

```php?start_inline=1
float cubicBezierDelta( 
    Freesewing\Point $from, 
    Freesewing\Point $cp1, 
    Freesewing\Point $cp2, 
    Freesewing\Point $to 
)
```
Finds the delta (between 0 and 1) of [`Point`](Point) `$split`
on a Bezier curve.

For example, if `$split` is exactly halfway the curve, this will
return 0.5.

#### Typical use
{:.no_toc}

Used only in [`Part::splitCurve`](Part#splitcurve). 

#### Parameters
{:.no_toc}

This expects four [`Point`](Point) objects that describe the Bezier curve.

#### Return value
{:.no_toc}

Returns a `float` between 0 and 1, indicating the position along the curve
of the [`Point`](Point) `$split`

### calculateSplitCurvePoints

```php?start_inline=1
array calculateSplitCurvePoints( 
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
to [`BezierToolbox::cubicBezierDelta`](BezierToolbox#cubicBezierDelta)

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

### bezierCircle

```php?start_inline=1
float bezierCircle( 
    float $radius 
)
```
Returns `$radius * 4 * (sqrt(2) - 1) / 3` which is the radius that mimics a circle segment
in a Bezier Curve.

You can't make a perfect circle with a cubic Bezier curve, but you can come close by
using by using the value returned by this method to offset your control points.

Delta `$t` is a value between 0 and 1 that indicates how far into 
the curve we need to split. It is typically the result of an earlier call
to [`BezierToolbox::cubicBezierDelta`](BezierToolbox#cubicBezierDelta)

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





{% include classFooter.html %}

---
layout: page
title: Pattern design tutorial
permalink: /designer/tutorial/
---
In this turorial, I'll show you how to design a sewing pattern on the Freesewing platform.

## Things you should know before we start

### You'll be writing code

Freesewing is a software project, and the pattern we are going to design will be a small _module_ that plugs into it.

>_If you've never written any code, don't panic. We'll take it one step at a time._

### What is a pattern?

We'll be designing a _recipe_ for a pattern that people can add their ingredients to. Those ingredients will be measurements and options.

For the sake of simplicity, when we talk about designing a pattern, we are designing the pattern recipe.

### Connecting the dots

Designing sewing patterns is a matter of connecting the dots. Traditionally, you'd start out with a large sheet of paper, mark a point somewhere, then measure down to the next point, sideways to yet another point, and so on. 

In the end, you draw lines and curves to connect these _points_, and you have your pattern.

Good news, we'll be doing exactly the same thing. Just not on paper, but in code.

### Patterns are made up of parts

Sewing patterns are typically made up of different parts. The front, the back, the sleeve, and so on. A pattern is not much more than a bunch of parts grouped together on a page.

### Things you should know: Summary

Ok, by now you've learned that:

- you'll be writing code
- you'll be making a pattern recipe, to which people can add measurements and options
- a pattern is made up of pattern parts
- we'll be adding points and connecting them with lines and curves

## Kicking the tires

### Your very first point

Time to get to work, we are going to add our very first point, like this:

```php?start_inline=1
$p->newPoint(1, 0, 0, 'Look, I am a point');
```
_(Let us ignore the `$p->` bit, we'll get to that later)_

To add a point to our pattern, we used the `newPoint` method, which takes 4 parameters:

- The name of the point. We picked `1` because we're lazy like that.
- The X-coordinate. We picked `0`.
- The Y-coordinate. We picked `0` (zero) again.
- [optional] A description. We went for `Look, I am a point`.

Now let's have a look at the result:

{% include figure.html 
    nr="1" 
    description="Your very first point" 
    url="https://api.freesewing.org/?service=draft&pattern=Tutorial&theme=Designer&parts=figure1&onlyPoints=1" 
%}

As you can see, we have a single point, indicated by that litte mark. Yay! :)

> #### The Designer theme
>
> Note that normally, points aren't shown on patterns. 
> In this case, we have rendered our pattern with the [Designer theme](../../themes/designer/), which shows extra information to make your designer life easier.

What is not very obvious from the image above, but you might already have figured it out, is that this point sits at coordinates `0,0`. In other words, at the intersection of the X and Y axis.

What you also can't see is the description. But if you [open this example in another window](https://api.freesewing.org/?service=draft&pattern=Tutorial&theme=Designer&parts=figure1&onlyPoints=1), you can mouse over it and the description would show up along with the coordinates (once again, thanks to [the Designer theme](../../themes/designer/)).

> #### The art of naming things
>
> We're going to be adding a bunch of other points. And later in this tutorial, we'll not just be adding points with their coordinates, but we'll be doing things like: "add a point exactly between those two points".
> 
> In other words, we need to be able to reference these points. That's why we give points a name. A name can be a number like `1`, `2`, `47`, or `-219.12`. But it can also be a string, as long as there's no spaces in it. We could have named our point `myFirstPoint` but that would have been cumbersome to type everytime we needed to reference it, so we went with `1`.
>
> Naming points is a balance between brevity (numbers are easy and quick to type) and verbosity (naming a point `dartTip` is a lot more self-explanatory than naming it `43`).
 
### Understanding the coordinate system: Your second point

Before we get to drawing things, you need to understand that the coordinate system originates from the top left.

Let's add a second point to illustrate that:

```php?start_inline=1
$p->newPoint('mySecondPoint', 100, 50);
```

> I've named this point `mySecondPoint` and I chose not to provide a description. Just so know that's possible.

I've added a point with an X-coordinate of 100, and an Y-coordinate of 50. Below you can see where the second point is:

{% include figure.html 
    nr="2" 
    description="Our second point sits the the right of and below our initial point."
    url="https://api.freesewing.org/?service=draft&pattern=Tutorial&theme=Designer&parts=figure2&onlyPoints=mySecondPoint,1" 
%}

You might have expected our second point to be higher than the first one. But the coordinate system originates at the top left.

In other words, `0,0` is top left, and `100,50` is 100 to the right and 50 down (and not up).

An easy way to remember this is to keep in mind that the coordinate system works exactly as you read this text. Top-left is the start, and as you progress with reading, you go right and down. Gowing up or left is going backwards.

{% include figure.html 
    nr="3" 
    description="The coordinate system runs from left to right, and top to bottom."
    url="https://api.freesewing.org/?service=draft&pattern=Tutorial&theme=Designer&parts=figure3&onlyPoints=mySecondPoint,1" 
%}

> Patterns are generated in SVG, short for [Scalable Vector Graphics](https://en.wikipedia.org/wiki/Scalable_Vector_Graphics). SVG is an open standard, and the coordinate system is defined in it.

### Your first path: A line

Now that we've got two points, we can draw a line. Here's how to do that:

```php?start_inline=1
$p->newPath(1,'M 1 L mySecondPoint');
```

(Again, let's ignore the $p-> bit, we’ll get to that later)

To add a path to our pattern, we used the newPath method, which takes 3 parameters:

- The **name** of the path. Just like with points, we need to name our paths so we can reference them later. We picked `1` path and point names are kept seperately. Just because there's a point `1` doesn't mean you can't have a path `1`.
- The **pathstring**
- [optional] An array of **attributes** for the path

The name of a path is exactly like the name of a point. Same rules apply (numbers or string-without-spaces) and they serve the same purpose.

The second parameter,  the **pathstring** is what it's all about. In this case, it says:

- `M 1` : `M`ove to point `1`
- `L mySecondPoint` : Draw a `L`ine to point `mySecondPoint`

Which gives us this result:

{% include figure.html 
    nr="4" 
    description="Look mom, a line!"
    url="https://api.freesewing.org/?service=draft&pattern=Tutorial&theme=Designer&parts=figure4&onlyPoints=mySecondPoint,1" 
%}

The third and optional parameter allows us to add an array of attributes for the path.
An array is a data element holding key/value pairs. They are written as such:

```php?start_inline=1
[ 
    'key1' => 'value1', 
    'key2' => 'value2',
]
```
As an example, if we would have added our path like this:

```php?start_inline=1
$p->newPath(1,'M 1 L mySecondPoint', ['class' => 'helpline']);
```

The result would have looked like this:

{% include figure.html 
    nr="11" 
    description="A path with its class attribute set to helpline"
    url="https://api.freesewing.org/?service=draft&pattern=Tutorial&theme=Designer&parts=figure11&onlyPoints=1,mySecondPoint"
%}

> The **class** attribute determines which [CSS](https://en.wikipedia.org/wiki/Cascading_Style_Sheets) classes will be added to the path.
> The classes are defined in the different themes.

### Understanding pathstrings: On M L C and Z

While a path can hold other information to control its look and feel, its most important property is the pathstring.

Pathstrings are a way to describe a path in text, and they support the following **operations**:

- `M` : Move. The move operation expects 1 point name of the point to move to. Moving does not draw anything. It's like moving across the paper without putting your pencil down.
- `L` : Line. The line operation expects 1 point name of the point to draw a line to.
- `C` : Curve. The curve operation expect 3 point namess. Two points that control the curve (so-called _control points_) and point at the curve endpoint.
- `Z` or `z` : Close path. This will close your path by drawing a line from wherever you are now to where your path started.

> Operations are seperated by spaces, as are the point names that follow them.  

Some examples will make this all very clear. Let's start with a clean slate and add some points:

```php?start_inline=1
$p->newPoint(1, 0, 0);
$p->newPoint(2, 100, 0);
$p->newPoint(3, 100, 50);
$p->newPoint(4, 0, 50);
$p->newPoint(5, 50, 25);
```

{% include figure.html 
    nr="5" 
    description="Five points ready to draw some paths"
    url="https://api.freesewing.org/?service=draft&pattern=Tutorial&theme=Designer&parts=figure5&onlyPoints=1,2,3,4,5" 
%}

#### A line example

```php?start_inline=1
$p->newPath(1,'M 5 L 2 L 3');
```
{% include figure.html 
    nr="7" 
    description="A path with two line operations"
    url="https://api.freesewing.org/?service=draft&pattern=Tutorial&theme=Designer&parts=figure6&onlyPoints=1,2,3,4,5" 
%}

We've `M`oved to point `5` (the one in the middle) and drew a `L`ine to point `2`, followed by a `L`ine to point `3`.

#### A closed path example

```php?start_inline=1
$p->newPath(1,'M 5 L 2 L 3 z');
```
{% include figure.html 
    nr="7" 
    description="A path with two line operations, and a close path operation"
    url="https://api.freesewing.org/?service=draft&pattern=Tutorial&theme=Designer&parts=figure7&onlyPoints=1,2,3,4,5" 
%}

We did the same thing as before, but we added a `z` operation at the end to close the path.

#### A curve example

```php?start_inline=1
$p->newPath(1,'M 1 C 2 3 4');
```
{% include figure.html 
    nr="8" 
    description="A path with a curve operation"
    url="https://api.freesewing.org/?service=draft&pattern=Tutorial&theme=Designer&parts=figure8&onlyPoints=1,2,3,4,5" 
%}

Curves have four points that define them:

- The start point
- The start control point
- The end control point
- The end point

Like all operations, the start point is wherever you are now. So the `C`urve operation expects only the 3 other points.
The last one is the point where the curve will end. The first one controls the curve on the start side, and the second one controls the cuve at the end side.

These are [Bézier curves](https://en.wikipedia.org/wiki/B%C3%A9zier_curve), they are the go-to way to represent curves in computers, and more intuitive than you might think.
Below is an example you can play with to see how the different points influence the curve:

{% include bezier_demo.html %}

#### A mixed example

```php?start_inline=1
$p->newPath(1,'M 5 L 1 C 2 3 4 z');
```
{% include figure.html 
    nr="9" 
    description="A path with a line, curve, and close path operations"
    url="https://api.freesewing.org/?service=draft&pattern=Tutorial&theme=Designer&parts=figure9&onlyPoints=1,2,3,4,5" 
%}

We started by moving to point 5 (the one in the middle) and drawing a line to point 1 (the top-left one). Then we drew our curve as in the previous example, and we ended by closing the path, which drew a line from the end of our curve back to point 5 where we started.

### Kicking the tires: Summary

In this section, you've learned that:

- You can use the `newPoint` method to add points to your pattern part
- You can use the `newPath` method to add paths to your pattern part
- Points and Paths have a _name_ that allows us to reference them later 
- The coordinate system starts at the topleft. The X-axis goes from left to right, the Y-axis from top to bottom
- The _pathstring_ of a path determines how lines and curves are drawn
- The pathstring supports the M, L, C and z operations for `M`ove, `L`ine, `C`urve and close path (`z`)
- The `z` operation stands on its own
- The `M` and `L` operation expect to be followed by a point that is the endpoint of the operation
- The `C` operation expects 3 points that are the start control point, end control point, and end point respectively

## Other elements you can add to your pattern

Now that we know how to connect the dots by adding points and then using a path to draw lines and curves with those points, it's time to look at some other things we can add to our pattern.

### Snippet

A snippet is a small re-usable bit of SVG markup that you can add to a pattern. Snippets are defined in the theme you are using, and need to be anchored on a point to determine their placement.

Let's look at an example:

```php?start_inline=1
$p->newPoint(1, 20, 10);
$p->newPoint(2, 40, 10);
$p->newPoint(3, 60, 10);

$p->newSnippet(1, 'notch', 1);
$p->newSnippet(2, 'button', 2);
$p->newSnippet(3, 'buttonhole', 3);
```
{% include figure.html 
    nr="10" 
    description="A notch, button, and buttonhole snippet from the Basic theme"
    url="https://api.freesewing.org/?service=draft&pattern=Tutorial&theme=Basic&parts=figure10" 
%}

Above, you can see the notch, button, and buttonhole snippets that are included in the Basic theme. 

The `newSnippet` method takes 4 parameters:

- The name of the snippet (you know what that means and is used for by now)
- The ID of the snippet as defined by the theme
- The name of the anchor point where the snippet should be placed
- [optional] An array of attributes for the snippet (just like path had)
- [optional] A description for the snippet

### Text

Text is just that, text. Here's an example that shows how to add text to your pattern:

```php?start_inline=1
$p->newPoint(1, 30, 10);
        
$p->newText(1, 1, 'Hello world');
```
{% include figure.html 
    nr="12" 
    description="Text rendered in the Basic theme"
    url="https://api.freesewing.org/?service=draft&pattern=Tutorial&theme=Basic&parts=figure12" 
%}

The `newText` method takes 4 parameters:

- The name of the text
- The name of the point the text should be anchored on
- The actual text
- [optional] An array of attributes for the text

### Title

Every pattern part typically has a title and some instructions, there's some handy shortcut methods that make adding these a snap.

```php?start_inline=1
$p->newPoint('titleAnchor', 50, 10);

$p->addTitle('titleAnchor', 3, 'French cuff', 'Cut 4x from fabric');;
```

The `addTitle` method takes 4 parameters:

- The name of the point to anchor the title on
- A part number
- The part title
- Extra text
- [optional] A mode that can be one of: `default`, `vertical`, `horizontal`, `small`, `vertical-small`, `horizontal-small`

As each part has only one title, we don't need to give this title a name to refer to it later.
Simple add the part number, part title, and optional extra text.

{% include figure.html 
    nr="13" 
    description="A title rendered in the Basic theme"
    url="https://api.freesewing.org/?service=draft&pattern=Tutorial&theme=Basic&parts=figure13" 
%}

The last parameter, the optional mode, controls how the title will be rendered on your pattern part. 

The example above is the default layout, which you get if you don't specify a mode, or use `default` as mode. Here are the other modes: 

```php?start_inline=1
$p->addTitle('titleAnchor', 3, 'French cuff', 'Mode small','small');;
```

{% include figure.html 
    nr="14" 
    description="A title rendered in small mode in the Basic theme"
    url="https://api.freesewing.org/?service=draft&pattern=Tutorial&theme=Basic&parts=figure14" 
%}

```php?start_inline=1
$p->addTitle('titleAnchor', 3, 'French cuff', 'Mode vertical','vertical');;
```

{% include figure.html 
    nr="15" 
    description="A title rendered in vertical mode in the Basic theme"
    url="https://api.freesewing.org/?service=draft&pattern=Tutorial&theme=Basic&parts=figure15" 
%}

```php?start_inline=1
$p->addTitle('titleAnchor', 3, 'French cuff', 'Mode vertical-small','vertical-small');;
```

{% include figure.html 
    nr="16" 
    description="A title rendered in horizontal mode in the Basic theme"
    url="https://api.freesewing.org/?service=draft&pattern=Tutorial&theme=Basic&parts=figure16" 
%}

```php?start_inline=1
$p->addTitle('titleAnchor', 3, 'French cuff', 'Mode horizontal-small','horizontal-small');;
```

{% include figure.html 
    nr="17" 
    description="A title rendered in horizontal-small mode in the Basic theme"
    url="https://api.freesewing.org/?service=draft&pattern=Tutorial&theme=Basic&parts=figure17" 
%}

```php?start_inline=1
$p->addTitle('titleAnchor', 3, 'French cuff', 'Mode small','small');;
```

{% include figure.html 
    nr="18" 
    description="A title rendered in vertical-small mode in the Basic theme"
    url="https://api.freesewing.org/?service=draft&pattern=Tutorial&theme=Basic&parts=figure18" 
%}


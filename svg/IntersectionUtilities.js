/*****
*
*   IntersectionUtilities.js
*
*   copyright 2002, Kevin Lindsey
*
*****/

/*****
*
*   globals
*
*****/
var svgns  = "http://www.w3.org/2000/svg";
var azap, mouser;
var points = new Array();
var shapes = new Array();
var info;


/*****
*
*   init
*
*****/
function init(e) {
    if ( window.svgDocument == null )
        svgDocument = e.target.ownerDocument;

    azap   = new AntiZoomAndPan();
    mouser = new Mouser();

    var infoElem = svgDocument.getElementById("info");
    var background = svgDocument.getElementById("background");

    info = infoElem.firstChild;
    
    azap.appendNode(infoElem);
    azap.appendNode(mouser.svgNode);
    azap.appendNode(background);

    loadShapes();
    showIntersections();
}


/*****
*
*   loadShapes
*
*****/
function loadShapes() {
    var children = svgDocument.documentElement.childNodes;

    for ( var i = 0; i < children.length; i++ ) {
        var child = children.item(i);

        if ( child.nodeType == 1 ) {
            // found element node
            var edit  = child.getAttributeNS(
                "http://www.kevlindev.com/gui",
                "edit"
            );
            
            if ( edit != null && edit != "" ) {
                // ignore value for now
                var shape;

                switch ( child.localName ) {
                    case "circle":  shape = new Circle(child);    break;
                    case "ellipse": shape = new Ellipse(child);   break;
                    case "line":    shape = new Line(child);      break;
                    case "path":    shape = new Path(child);      break;
                    case "polygon": shape = new Polygon(child);   break;
                    case "rect":    shape = new Rectangle(child); break;
                    default:
                        // do nothing for now
                }

                if ( shape != null ) {
                    shape.realize();
                    shape.callback = showIntersections;
                    shapes.push(shape);
                }
            }
        }
    }
}


/*****
*
*   showIntersections
*
*****/
function showIntersections() {
    if ( shapes.length >= 2 ) {
        var inter = Intersection.intersectShapes( shapes[0], shapes[1] );

        info.data = inter.status;
        for ( var i = 0; i < inter.points.length; i++ ) {
            var coord = inter.points[i];

            if ( i >= points.length ) {
                var point = svgDocument.createElementNS(svgns, "use");

                point.setAttributeNS(
                    "http://www.w3.org/1999/xlink",
                    "href",
                    "#intersection"
                );
                svgDocument.documentElement.appendChild(point);
                points.push(point);
            }
            points[i].setAttributeNS(null, "x", coord.x);
            points[i].setAttributeNS(null, "y", coord.y);
            points[i].setAttributeNS(null, "display", "inline");
        }

        for ( var i = inter.points.length; i < points.length; i++ ) {
            points[i].setAttributeNS(null, "display", "none");
        }
    }
}

<h2 id="basic-shapes">Basic Shapes</h2>
<h3 id="rect">rect()</h3>
<h4>Examples</h4>
<pre>
# Draw a rectangle at location (10, 20) with a width of 50 and height of 25.
rect(10, 20, 50, 25)
</pre>
<h4>Description</h4>
<p>
Draws a rectangle on the canvas. By default, the first two parameters set the location of the upper-left corner, the third sets the width, and the fourth sets the height. The way these parameters are interpreted, may be changed with the <a href="#rectMode">rectMode()</a> function.
</p>
<h4>Syntax</h4>
<p>rect(x, y, w, h)</p>
<h4>Parameters</h4>
<p>x - The x coordintate of the rectangle.</p>
<p>y - The y coordintate of the rectangle.</p>
<p>w - The width of the rectangle.</p>
<p>h - The height of the rectangle.</p>
<hr />
<h3 id="circle">circle()</h3>
<h4>Examples</h4>
<pre>
# Draw a circle at location (100, 200) with a radius of 50.
circle(100, 200, 50)
</pre>
<h4>Description</h4>
<p>
Draws a circle on the canvas. By default, the first two parameters set the location of the center of the circle, and the third sets the radius. The way these parameters are interpreted, may be changed with the <a href="#circleMode">circleMode()</a> function.
</p>
<h4>Syntax</h4>
<p>circle(x, y, radius)</p>
<h4>Parameters</h4>
<p>x - The x coordintate of the circle.</p>
<p>y - The y coordintate of the circle.</p>
<p>radius - The radius of the circle.</p>
<hr />
<h3 id="ellipse">ellipse()</h3>
<h4>Examples</h4>
<pre>
# Draw an ellipse at location (100, 200) with an X radius of 50 and a Y radius of 75.
ellipse(100, 200, 50, 75)
</pre>
<h4>Description</h4>
<p>
Draws an ellipse (oval) on the canvas. By default, the first two parameters set the location of the center of the circle, the third sets the X radius, and the fourth sets the Y radius. The way these parameters are interpreted, may be changed with the <a href="#circleMode">circleMode()</a> function.
</p>
<h4>Syntax</h4>
<p>ellipse(x, y, radiusX, radiusY)</p>
<h4>Parameters</h4>
<p>x - The x coordintate of the ellipse.</p>
<p>y - The y coordintate of the ellipse.</p>
<p>radiusX - The X radius of the ellipse.</p>
<p>radiusY - The Y radius of the ellipse.</p>
<hr />
<h3 id="arc">arc()</h3>
<h4>Examples</h4>
<pre>
# Draw an arc at location (50, 50) with an X radius of 40 and a Y radius of 30. The arc spans from 0 to 270 degrees.
arc(50, 50, 40, 30, 0, 270)
</pre>
<h4>Description</h4>
<p>
Draws an arc (a portion of an ellipse) on the canvas. By default, the first two parameters set the location of the center of the circle, the third sets the X radius, and the fourth sets the Y radius. The fifth parameter is the start angle and the sixth is the end angle. The arc is always drawn clockwise from the start angle to the end angle. The way these parameters are interpreted, may be changed with the <a href="#circleMode">circleMode()</a> function. By default the start and end angle are specified in degrees. This can be changed to radians with the <a href="#angleMode">angleMode()</a> function.
</p>
<h4>Syntax</h4>
<p>arc(x, y, radiusX, radiusY, startAngle, endAngle)</p>
<h4>Parameters</h4>
<p>x - The x coordintate of the arc.</p>
<p>y - The y coordintate of the arc.</p>
<p>radiusX - The X radius of the arc.</p>
<p>radiusY - The Y radius of the arc.</p>
<p>startAngle - The starting angle of the arc.</p>
<p>endAngle - The ending angle of the arc.</p>
<hr />
<h3 id="line">line()</h3>
<h4>Examples</h4>
<pre>
# Draw a line starting at (40, 20) and finishing at (60, 40).
line(40, 20, 60, 40)
</pre>
<pre>
# Draw 3 lines of different colours that give a 3D effect.
stroke(0, 0, 0)
line(40, 30, 95, 30)
stroke(120, 120, 120)
line(95, 30, 95, 85)
stroke(255, 255, 255)
line(95, 85, 40, 85)
</pre>
<h4>Description</h4>
<p>
Draws an line between two points to the screen. By default the line has a width of a single pixel. This can be modified by the <a href="#strokeWeight">strokeWeight()</a> function. The colour of a line can be changed by calling the <a href="#stroke">stroke()</a> function.
</p>
<h4>Syntax</h4>
<p>line(x1, y1, x2, y2)</p>
<h4>Parameters</h4>
<p>x1 - The x coordintate of the starting point.</p>
<p>y1 - The y coordintate of the starting point.</p>
<p>x2 - The x coordintate of the ending point.</p>
<p>y2 - The y coordintate of the ending point.</p>
<hr />
<h3 id="point">point()</h3>
<h4>Examples</h4>
<pre>
# Draw a point at (40, 20).
point(40, 20)
</pre>
<pre>
# Draw a blue point at (50, 30) that is 20 pixels in size.
stroke(0, 0, 255)
strokeWeight(20)
point(50, 30)
</pre>
<h4>Description</h4>
<p>
Draws a pixel to the screen at the position given by the two parameters. The first parameter specifies the x position and the second parameter specifies the y position. By default the pixel has a size of a one pixel. This can be modified by the <a href="#strokeWeight">strokeWeight()</a> function. The colour of a point can be changed by calling the <a href="#stroke">stroke()</a> function.
</p>
<h4>Syntax</h4>
<p>point(x, y)</p>
<h4>Parameters</h4>
<p>x - The x coordintate.</p>
<p>y - The y coordintate.</p>
<hr />
<h3 id="square">square()</h3>
<h4>Examples</h4>
<pre>
# Draw a square at location (10, 20) with a length of 50 pixels.
square(10, 20, 50)
</pre>
<h4>Description</h4>
<p>
Draws a square on the canvas. By default, the first two parameters set the location of the upper-left corner, the third sets the length.
</p>
<h4>Syntax</h4>
<p>square(x, y, l)</p>
<h4>Parameters</h4>
<p>x - The x coordintate of the square.</p>
<p>y - The y coordintate of the square.</p>
<p>l - The length of the square.</p>
<hr />
<h3 id="triangle">triangle()</h3>
<h4>Examples</h4>
<pre>
# Draw a triangle specified by the three points (50, 75), (25, 100), and (75, 100).
triangle(50, 75, 25, 100, 75, 100)
</pre>
<h4>Description</h4>
<p>
Draws a triangle on the canvas specified by three points.
</p>
<h4>Syntax</h4>
<p>triangle(x1, y1, x2, y2, x3, y3)</p>
<h4>Parameters</h4>
<p>x1 - The x coordintate of the first point.</p>
<p>y1 - The y coordintate of the first point.</p>
<p>x2 - The x coordintate of the second point.</p>
<p>y2 - The y coordintate of the second point.</p>
<p>x3 - The x coordintate of the third point.</p>
<p>y3 - The y coordintate of the third point.</p>
<hr />
<h3 id="quad">quad()</h3>
<h4>Examples</h4>
<pre>
# Draw a quad specified by the four points (50, 75), (25, 100), (75, 100), and (100, 75).
quad(50, 75, 25, 100, 75, 100, 100, 75)
</pre>
<h4>Description</h4>
<p>
Draws a quadrilateral (a four sided polygon) on the canvas specified by four points.
</p>
<h4>Syntax</h4>
<p>quad(x1, y1, x2, y2, x3, y3, x4, y4)</p>
<h4>Parameters</h4>
<p>x1 - The x coordintate of the first point.</p>
<p>y1 - The y coordintate of the first point.</p>
<p>x2 - The x coordintate of the second point.</p>
<p>y2 - The y coordintate of the second point.</p>
<p>x3 - The x coordintate of the third point.</p>
<p>y3 - The y coordintate of the third point.</p>
<p>x4 - The x coordintate of the fourth point.</p>
<p>y4 - The y coordintate of the fourth point.</p>
<hr />
<h3 id="rectMode">rectMode()</h3>
<h4>Examples</h4>
<pre>
rectMode(CORNER)
fill(0, 0, 255)
# draw a blue rectangle with rectMode(CORNER)
rect(30, 30, 60, 60)
rectMode(CORNERS)
fill(255, 0, 0)
# draw a red rectangle with rectMode(CORNERS)
rect(30, 30, 60, 60)
</pre>
<pre>
rectMode(CENTER)
fill(0, 0, 255)
# draw a blue rectangle with rectMode(CENTER)
rect(30, 30, 60, 60)
rectMode(CORNER)
fill(255, 0, 0, 0.5)
# draw a red rectangle with rectMode(CORNER)
rect(30, 30, 60, 60)
</pre>
<h4>Description</h4>
<p>
Changes the way the rect() function uses the paramters passed to it.</p>
<p>The default mode is CORNER, which indicates that the first two parameters are the coordinates of the top left corner, and the third and fourth parameters specify the width and the height.</p>
<p>The mode CORNERS indicates the first two parameters are the coordinates of the top left corner, and the third and fourth specify the bottom right coordinates.</p>
<p>The mode CENTER indicates the first two parameters are the coordinates of the center of the rectangle, and the third and fourth specify the width and height.</p>
<h4>Syntax</h4>
<p>rectMode(mode)</p>
<h4>Parameters</h4>
<p>mode - Can be CORNER, CORNERS, or CENTER</p>
<hr />
<h3 id="circleMode">circleMode()</h3>
<h4>Examples</h4>
<pre>
circleMode(CENTER)
fill(0, 0, 255)
# draw a blue circle with circleMode(CENTER)
circle(100, 100, 50)
circleMode(CORNER)
fill(255, 0, 0, 0.5)
# draw a red circle with circleMode(CORNER)
circle(100, 100, 50)
</pre>
<h4>Description</h4>
<p>
Changes the way the circle(), ellipse(), and arc() functions use the paramters passed to them.</p>
<p>The default mode is CENTER, which indicates that the first two parameters are the coordinates of the center of the shape. The remaining parameters refer to the radius for the circle() function, and the X radius and Y radius for the ellipse() and arc() functions.</p>
<p>The mode CORNER indicates the first two parameters are the coordinates of the top left corner of the shape. The meaning of any extra parameters remain unchanged.</p>
<h4>Syntax</h4>
<p>circleMode(mode)</p>
<h4>Parameters</h4>
<p>mode - Can be CORNER, or CENTER</p>
<hr />
<h3 id="strokeWeight">strokeWeight()</h3>
<h4>Examples</h4>
<pre>
strokeWeight(1)
line(10, 10, 100, 10)
strokeWeight(2)
line(10, 20, 100, 20)
strokeWeight(4)
line(10, 30, 100, 30)
strokeWeight(8)
line(10, 40, 100, 40)
</pre>
<h4>Description</h4>
<p>
Sets the width of any lines, points and the border around shapes. All widths are specified in pixels.
</p>
<h4>Syntax</h4>
<p>strokeWeight(weight)</p>
<h4>Parameters</h4>
<p>weight - the weight of the stroke in pixels.</p>
<hr />

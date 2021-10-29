<h2 id="maths">Maths</h2>
<h3 id="constrain">constrain()</h3>
<h4>Examples</h4>
<pre>
setCanvasSize(100, 100)

forever:
    background(200)

    leftWall = 25
    rightWall = 75

    # xm is just the mouseX, while
    # xc is the mouseX, but constrained
    # between the leftWall and rightWall!
    xm = mouseX
    xc = constrain(mouseX, leftWall, rightWall)

    # Draw the walls.
    stroke(150, 150, 150)
    line(leftWall, 0, leftWall, height)
    line(rightWall, 0, rightWall, height)

    # Draw xm and xc as circles.
    noStroke()
    fill(150, 150, 150)
    ellipse(xm, 33, 9, 9) # Not Constrained
    fill(0, 0, 0)
    ellipse(xc, 66, 9, 9) # Constrained
</pre>
<h4>Description</h4>
<p>
Constrains a value between a minimum and maximum value.
</p>
<h4>Syntax</h4>
<p>constrain(n, low, high)</p>
<h4>Parameters</h4>
<p>n - The number to constrain</p>
<p>low - The minimum limit</p>
<p>high - The maximum limit</p>
<h4>Returns</h4>
The constrained number
<hr />
<h3 id="dist">dist()</h3>
<h4>Examples</h4>
<pre>
from math import *
setCanvasSize(500, 400)
fill(0, 0, 0)
x1 = 20
y1 = height - 50
angleMode(RADIANS)

while True:
    saveState()
    background(220, 220, 220)

    x2 = mouseX
    y2 = mouseY

    line(x1, y1, x2, y2)
    circle(x1, y1, 5)
    circle(x2, y2, 5)

    d = int(dist(x1, y1, x2, y2))

    # Let's write d along the line we are drawing
    translate((x1+x2)/2, (y1+y2)/2)
    rotate(atan2(y2-y1, x2 - x1))
    text(d, 0, -20)
    restoreState()
</pre>
<h4>Description</h4>
<p>
Calculates the distance between two points.
</p>
<h4>Syntax</h4>
<p>dist(x1, y1, x2, y2)</p>
<h4>Parameters</h4>
<p>x1 - The x coordinate of the first point.</p>
<p>y1 - The y coordinate of the first point.</p>
<p>x2 - The x coordinate of the second point.</p>
<p>y2 - The y coordinate of the second point.</p>
<h4>Returns</h4>
The distance between two points as a floating point number.
<hr />
<h3 id="mapToRange">mapToRange()</h3>
<h4>Examples</h4>
<pre>
from math import *
setCanvasSize(500, 400)

while True:
    r = mapToRange(mouseX, 0, width, 0, 255)
    fill(int(r), 0, 0)
    circle(width/2, height/2, 50)
    sleep(0.005)
</pre>
<h4>Description</h4>
<p>
Re-maps a number from one range to another.
</p>
<h4>Syntax</h4>
<p>mapToRange(value, start1, stop1, start2, stop2, withinBounds)</p>
<h4>Parameters</h4>
<p>value - The incoming value to be converted.</p>
<p>start1 - The lower bound of the current range.</p>
<p>stop1 - The upper bound of the current range.</p>
<p>start2 - The lower bound of the target range.</p>
<p>stop2 - The upper bound of the target range.</p>
<p>withinBounds - Should the value be constrained to the target range.</p>
<h4>Returns</h4>
The converted number.
<hr />

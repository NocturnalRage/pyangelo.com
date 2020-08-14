<h2 id="maths">Maths</h2>
<h3 id="dist">dist()</h3>
<h4>Examples</h4>
<pre>
setCanvasSize(500, 400)
fill(0, 0, 0)
x1 = 20
y1 = height - 50
angleMode(RADIANS)

@loop_animation
background(220, 220, 220)

x2 = mouseX
y2 = mouseY

line(x1, y1, x2, y2)
circle(x1, y1, 5)
circle(x2, y2, 5)

d = int(dist(x1, y1, x2, y2))

# Let's write d along the line we are drawing
translate((x1+x2)/2, (y1+y2)/2)
rotate(math.atan2(y2-y1, x2 - x1))
text(d, 0, -20)
</pre>
<h4>Description</h4>
<p>
Calculates the distance between two points.
</p>
<h4>Syntax</h4>
<p>dist(x1, y1, x2, y2)</p>
<h4>Parameters</h4>
<p>x1 - The x coordintate of the first point.</p>
<p>y1 - The y coordintate of the first point.</p>
<p>x2 - The x coordintate of the second point.</p>
<p>y2 - The y coordintate of the second point.</p>
<h4>Returns</h4>
The distance between two points as a floating point number.
<hr />

<h2 id="animation">Animation</h2>
<h3 id="loop_animation">@loop_animation()</h3>
<h4>Examples</h4>
<pre>
setCanvasSize(500, 400)
radius = 25
y = -radius

@loop_animation
background(10, 100, 200)
fill(200, 100, 10)
circle(width/2, y, radius)
y = y + 1
if y > height + radius:
      y = -radius
</pre>
<h4>Description</h4>
<p>
The @loop_animation line must be positioned on it's own line and must not be indented. Any lines of code above the @loop_animation line will be executed a single time. All lines below the @loop_animation line will be executed repeatedly in a loop. This loop allows the programmer to create animations.
</p>
<hr />
<h3 id="setCanvasSize">setCanvasSize()</h3>
<h4>Examples</h4>
<pre>
setCanvasSize(640, 360)

@loop_animation
background(10, 100, 200)
</pre>
<h4>Description</h4>
<p>
Sets the size of the canvas that all drawings are written to. The first parameter specifies the width in pixels and the second the height.
</p>
<h4>Syntax</h4>
<p>setCanvasSize(x, y)</p>
<h4>Parameters</h4>
<p>x - The width of the canvas in pixels.</p>
<p>y - The height of the canvas in pixels.</p>
<hr />

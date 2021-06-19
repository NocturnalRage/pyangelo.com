<h2 id="colour">Colour</h2>
<h3 id="background">background()</h3>
<h4>Examples</h4>
<pre>
# Draw a yellow rectangle as the background of the canvas.
setCanvasSize(640, 360)
background(255, 255, 0)
</pre>
<h4>Description</h4>
<p>
Draws a rectangle the size of the canvas. The colour of the rectangle is specifed by the first three parameters representing an RGB colour. The function is typically called as part of loop to clear the canvas each frame. If a fourth parameter is passed it specifies an alpha value ranging from 0 to 1 where 0 is fully transparent and 1 specifies no transparency.
</p>
<h4>Syntax</h4>
<p>background(r, g, b, a)</p>
<h4>Parameters</h4>
<p>r - The red value of the colour ranging from 0 to 255.</p>
<p>g - The green value of the colour ranging from 0 to 255.</p>
<p>b - The blue value of the colour ranging from 0 to 255.</p>
<p>a - The alpha value of the background ranging from 0 to 1.</p>
<hr />
<h3 id="fill">fill()</h3>
<h4>Examples</h4>
<pre>
setCanvasSize(640, 360)
fill(100, 220, 100)
rect(50, 50, 100, 100)
</pre>
<h4>Description</h4>
<p>
Sets the colour used to fill shapes. The colour is specified using the RGB colour scheme. The first parameter represents the amount of red, the second the amount of green, and the third the amount of blue in the colour. If a fourth parameter is passed it represents the alpha value ranging from 0 to 1.
</p>
<h4>Syntax</h4>
<p>fill(r, g, b, a)</p>
<h4>Parameters</h4>
<p>r - The red value of the colour ranging from 0 to 255.</p>
<p>g - The green value of the colour ranging from 0 to 255.</p>
<p>b - The blue value of the colour ranging from 0 to 255.</p>
<p>a - The alpha value of the background ranging from 0 to 1.</p>
<hr />
<h3 id="noFill">noFill()</h3>
<h4>Examples</h4>
<pre>
setCanvasSize(640, 360)
noFill()
rect(50, 50, 100, 100)
</pre>
<h4>Description</h4>
<p>
Specifies that shapes should not be filled when drawn. If both <a href="#noStroke">noStroke()</a> and noFill() are called then nothing will be drawn to the screen.
</p>
<h4>Syntax</h4>
<p>noFill()</p>
<hr />
<h3 id="stroke">stroke()</h3>
<h4>Examples</h4>
<pre>
setCanvasSize(640, 360)
strokeWeight(5)
stroke(255, 0, 0)
rect(10, 10, 100, 75)
</pre>
<h4>Description</h4>
<p>
Sets the colour used to draw points, lines, and the border around shapes. The colour is specified using the RGB colour scheme. The first parameter represents the amount of red, the second the amount of green, and the third the amount of blue in the colour. If a fourth parameter is passed it represents the alpha value ranging from 0 to 1.
</p>
<h4>Syntax</h4>
<p>stroke(r, g, b, a)</p>
<h4>Parameters</h4>
<p>r - The red value of the colour ranging from 0 to 255.</p>
<p>g - The green value of the colour ranging from 0 to 255.</p>
<p>b - The blue value of the colour ranging from 0 to 255.</p>
<p>a - The alpha value of the background ranging from 0 to 1.</p>
<hr />
<h3 id="noStroke">noStroke()</h3>
<h4>Examples</h4>
<pre>
setCanvasSize(640, 360)
noStroke()
rect(50, 50, 100, 100)
</pre>
<h4>Description</h4>
<p>
Specifies that no stroke should be drawn for points, lines, and borders. If both noStroke() and <a href="#noFill">noFill()</a> are called then nothing will be drawn to the screen.
</p>
<h4>Syntax</h4>
<p>noStroke()</p>
<hr />

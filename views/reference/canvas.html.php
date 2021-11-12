<h2 id="canvasref">Canvas</h2>
<h3 id="setCanvasSize">setCanvasSize()</h3>
<h4>Examples</h4>
<pre>
setCanvasSize(640, 360)
</pre>
<h4>Description</h4>
<p>
Sets the size of the canvas that all drawings are written to. The first parameter specifies the width in pixels and the second the height. The third parameter is optional and specifies the direction of the y-axis. The constant CARTESIAN can be used to specify a y-axis that is used in maths and a constant of JAVASCRIPT can be used to specify the regular javascript y-axis which moves down the screen. If no value is provided as the third parameter, a default of CARTESIAN is used.
</p>
<h4>Syntax</h4>
<p>setCanvasSize(w, h, yAxisMode)</p>
<h4>Parameters</h4>
<p>w - The width of the canvas in pixels.</p>
<p>h - The height of the canvas in pixels.</p>
<p>yAxisMode - Optionally used to set the direction of the y-axis. Use the constants CARTESIAN or JAVASCRIPT. CARTESIAN is used as the default value if this parameter is not supplied.</p>
<hr />
<h3 id="noCanvas">noCanvas()</h3>
<h4>Examples</h4>
<pre>
noCanvas()
</pre>
<h4>Description</h4>
<p>
Hides the canvas from the page. This may be useful if you are running a text only Python program.
</p>
<h4>Syntax</h4>
<p>noCanvas()</p>
<h4>Parameters</h4>
<p>This function takes no parameters.</p>
<hr />
<h3 id="focusCanvas">focusCanvas()</h3>
<h4>Examples</h4>
<pre>
setCanvasSize(640, 360)
background(220, 220, 220)
input("Press ENTER to continue")
focusCanvas()
</pre>
<h4>Description</h4>
<p>
Places the focus back on the canvas. This is necessary if you wish to receive keyboard events that occur on the canvas and the focus has been moved away. The focus can be moved when a user responds to an input function or clicks away from the canvas. The focus can be returned by the user clicking on the canvas but this function gives you a programmatic way to return focus. 
</p>
<h4>Syntax</h4>
<p>focusCanvas()</p>
<h4>Parameters</h4>
<p>This function takes no parameters.</p>
<hr />

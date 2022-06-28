<h2 id="mouse">Mouse</h2>
<h3 id="mouseX">mouseX</h3>
<h4>Description</h4>
<p>
This variable holds the current horizontal position of the mouse. The value is the number of pixels from the origin (0, 0) of the canvas.
</p>
<h4>Syntax</h4>
<p>mouseX</p>
<h3 id="mouseY">mouseY</h3>
<h4>Description</h4>
<p>
This variable holds the current vertical position of the mouse. The value is the number of pixels from the origin (0, 0) of the canvas.
</p>
<h4>Syntax</h4>
<p>mouseY</p>
<h3 id="mouseIsPressed">mouseIsPressed</h3>
<h4>Description</h4>
<p>
This boolean variable is True when the mouse is currently pressed, otherwise it is False.
</p>
<h4>Syntax</h4>
<p>mouseIsPressed</p>
<hr />
<h3 id="wasMousePressed">wasMousePressed()</h3>
<h4>Examples</h4>
<pre>
setCanvasSize(400, 400)
forever:
    if wasMousePressed():
        print("Mouse was pressed")
    sleep(0.005)
</pre>
<h4>Description</h4>
<p>
Returns True if the mouse has been pressed and not yet released before this function is called. Otherwise, it returns False. Once the mouse has been pressed and the function has been called, the function will then return False until the mouse is pressed again. This is different from the mouseIsPressed inbuilt boolean variable which continues to be set to True until the mouse is released.
</p>
<h4>Syntax</h4>
<p>wasMousePressed()</p>
<h4>Parameters</h4>
<p>This function takes no parameters.</p>
<hr />

<h2 id="transformation">Transformation</h2>
<h3 id="translate">translate()</h3>
<h4>Examples</h4>
<pre>
background(220, 220, 220)
rect(0, 0, 50, 50) # Draw rect at original 0,0
translate(50, 50)
rect(0, 0, 50, 50) # Draw rect at new 0,0
translate(25, 10)
rect(0, 0, 50, 50) # Draw rect at new 0,0
</pre>
<h4>Description</h4>
<p>
Moves the position of the origin. The first parameter specifies the number of pixels along the x axis, and the second paramter specifies the number of pixels along the y axis.
</p>
<p>
If tranlate is called twice, the effects are cumulative. So calling translate(10, 10) followed by translate(20, 20) is the same as calling translate(30, 30). However, all translations are reset each time the <a href="#loop_animation">@loop_animation</a> is run. The <a href="#saveState">saveState()</a> and <a href="#restoreState">restoreState()</a> functions can be used to save and restore transformations.
</p>
<h4>Syntax</h4>
<p>translate(x, y)</p>
<h4>Parameters</h4>
<p>x - The number of pixels to move the origin along the x axis.</p>
<p>y - The number of pixels to move the origin along the y axis.</p>
<hr />
<h3 id="rotate">rotate()</h3>
<h4>Examples</h4>
<pre>
background(220, 220, 220)
translate(width/2, height/2) # translate to the center of the screen
rectMode(CENTER)
rotate(45)
rect(0, 0, 50, 50)
</pre>
<h4>Description</h4>
<p>
Rotates the shape by the angle specified in the only parameter. By default, the angle is in degrees. This can be changed to radians by using the <a href="#angleMode">angleMode()</a> function.
</p>
<p>
Shapes are rotated around the origin. Positive numbers rotate in a clockwise direction. Rotations are cumulative so calling rorate(45) followed by rotate(30) is the same as calling rotate(75). However, all rotations are reset each time the <a href="#loop_animation">@loop_animation</a> is run. The <a href="#saveState">saveState()</a> and <a href="#restoreState">restoreState()</a> functions can be used to save and restore transformations.
</p>
<h4>Syntax</h4>
<p>rotate(angle)</p>
<h4>Parameters</h4>
<p>angle - The number of of degrees or radians to rotate the shape depending on the angleMode.</p>
<hr />
<h3 id="saveState">saveState()</h3>
<h4>Examples</h4>
<pre>
background(220, 220, 220)
saveState()
translate(width/2, height/2) # translate to the center of the screen
rectMode(CENTER)
rotate(45)
rect(0, 0, 50, 50)

restoreState()
rectMode(CORNER)
rect(0, 0, 50, 50)
</pre>
<h4>Description</h4>
<p>
Saves the current drawing style settings and transformations. These can be restored using the <a href="#restoreState">restoreState()</a> function. This allows you to change the style and transformation settings and then return to the previous version of these settings.
</p>
<p>
This function saves the settings of the <a href="#fill">fill()</a>, <a href="#stroke">stroke()</a>, <a href="#strokeWeight">strokeWeight()</a>, <a href="#translate">translate()</a>, and <a href="#rotate">rotate()</a> functions. 
</p>
<h4>Syntax</h4>
<p>saveState()</p>
<hr />
<h3 id="restoreState">restoreState()</h3>
<h4>Examples</h4>
<pre>
background(220, 220, 220)
saveState()
translate(width/2, height/2) # translate to the center of the screen
rectMode(CENTER)
rotate(45)
rect(0, 0, 50, 50)

restoreState()
rectMode(CORNER)
rect(0, 0, 50, 50)
</pre>
<h4>Description</h4>
<p>
Restores the latest version of the drawing style settings and transformations. To save these settings the <a href="#saveState">saveState()</a> function must be used. This allows you to change the style and transformation settings and then return to the previous version of these settings.
</p>
<p>
This function restores the previously saved settings of the <a href="#fill">fill()</a>, <a href="#stroke">stroke()</a>, <a href="#strokeWeight">strokeWeight()</a>, <a href="#translate">translate()</a>, and <a href="#rotate">rotate()</a> functions. 
</p>
<h4>Syntax</h4>
<p>restoreState()</p>
<hr />

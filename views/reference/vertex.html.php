<h2 id="vertex_commands">Vertex</h2>
<h3 id="beginShape">beginShape()</h3>
<h4>Examples</h4>
<pre>
fill(200, 90, 90)
beginShape()
vertex(10, 10)
vertex(20, 100)
vertex(60, 110)
vertex(50, 50)
vertex(200, 5)
endShape()
</pre>
<h4>Description</h4>
<p>
The <a href="#beginShape">beginShape()</a>, <a href="#vertex">vertex()</a>, and <a href="#endShape">endShape()</a> functions allow you to create more complex shapes. The beginShape() function starts recording vertices that are added via the <a href="#vertex">vertex()</a> function.
</p>
<h4>Syntax</h4>
<p>beginShape()</p>
<hr />
<h3 id="vertex">vertex()</h3>
<h4>Examples</h4>
<pre>
fill(200, 90, 90)
beginShape()
vertex(10, 10)
vertex(20, 100)
vertex(60, 110)
vertex(50, 50)
vertex(200, 5)
endShape()
</pre>
<h4>Description</h4>
<p>
The vertex() function adds a point to the list of vertices that will be connected when the <a href="#endShape">endShape()</a> function is called. It takes two parameters, the x and y coordinates of the vertex to add.
</p>
<h4>Syntax</h4>
<p>vertex(x, y)</p>
<h4>Parameters</h4>
<p>x - The x coordinate of the vertex to add.</p>
<p>y - The y coordinate of the vertex to add.</p>
<hr />
<h3 id="endShape">endShape()</h3>
<h4>Examples</h4>
<pre>
fill(200, 90, 90)
beginShape()
vertex(10, 10)
vertex(20, 100)
vertex(60, 110)
vertex(50, 50)
vertex(200, 5)
endShape()
</pre>
<pre>
fill(200, 90, 90)
beginShape()
vertex(10, 10)
vertex(20, 100)
vertex(60, 110)
vertex(50, 50)
vertex(200, 5)
endShape(OPEN)
</pre>
<h4>Description</h4>
<p>
Draws a shape specified by the list of vertices added by calling <a href="#beginShape">beginShape()</a> followed by any number of <a href="#vertex">vertex()</a> function calls. By default the entire shape is closed by linking the last vertex back to the first. This can be changed by passing the constant OPEN as a parameter.</p>
<h4>Syntax</h4>
<p>endShape(mode)</p>
<h4>Parameters</h4>
<p>mode - CLOSE or OPEN. CLOSE specifies the shape should be closed and is the default where OPEN does not connect the last vertex to the first.</p>
<hr />

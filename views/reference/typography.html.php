<h2 id="typography">Typography</h2>
<p>Functions for loading and selecting fonts, and drawing text.</p>
<h3 id="text">text()</h3>
<h4>Examples</h4>
<pre>
setCanvasSize(400, 100)
background(220, 220, 220)
text("I love PyAngelo!", 20, 30)
</pre>
<pre>
setCanvasSize(800, 100)
background(220, 220, 220)
text("I'm a new font that is big!", 20, 30, 50, 'Times New Roman')
</pre>
<h4>Description</h4>
<p>
Draws text to the screen. The first 3 parameters are mandatory. The first specified the text to display. The second is the x position and the third is the y position. The fourth parameter is optional and is the size of the text. This defaults to 20 pixels. The fifth parameter is optional is the font to use. The font defaults to Arial.
</p>
<h4>Syntax</h4>
<p>text(text, x, y, fontSize, fontName)</p>
<h4>Parameters</h4>
<p>text - The text to display.</p>
<p>x - The x position of the top left of the text.</p>
<p>y - The y position of the top left of the text.</p>
<p>fontSize - The size of the text in pixels.</p>
<p>fontName - The type of font to use when displaying the text.</p>
<h3 id="loadFont">loadFont()</h3>
<h4>Examples</h4>
<pre>
setCanvasSize(450, 100)
background(220, 220, 220)
myFont = loadFont("/samples/fonts/arcade.ttf")
text("I love PyAngelo!", 20, 30, 50, myFont)
</pre>
<h4>Description</h4>
<p>Loads a font to the screen as specified in the first parameter.</p>
<h4>Syntax</h4>
<p>myFont = loadFont(filename)</p>
<h4>Parameters</h4>
<p>filename - The font file.</p>
<h3 id="setFont">setFont()</h3>
<h4>Examples</h4>
<pre>
setCanvasSize(450, 100)
background(220, 220, 220)
myFont = loadFont("/samples/fonts/arcade.ttf")
setFont(myFont)
text("I love PyAngelo!", 20, 30, 50)
</pre>
<h4>Description</h4>
<p>Sets the default font to use if one is not specified when calling text()</p>
<h4>Syntax</h4>
<p>setFont(font)</p>
<h4>Parameters</h4>
<p>font - The default font to use. Can be a font you have loaded or a font that is available in your browser.</p>
<hr />

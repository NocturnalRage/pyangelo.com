<h2 id="consoleref">Console</h2>
<h3 id="setConsoleSize">setConsoleSize()</h3>
<h4>Examples</h4>
<pre>
setConsoleSize(SMALL_SCREEN)
</pre>
<pre>
setConsoleSize(MEDIUM_SCREEN)
</pre>
<pre>
setConsoleSize(LARGE_SCREEN)
</pre>
<h4>Description</h4>
<p>
Sets the size of the console in pixels. The first and only parameter is an integer that must be between between 100 and 2000. The PyAngelo constants of SMALL_SCREEN (100), MEDIUM_SCREEN (500), and LARGE_SCREEN (1000) can be used.
</p>
<h4>Syntax</h4>
<p>setConsoleSize(size)</p>
<h4>Parameters</h4>
<p>size - The height of the console in pixels.</p>
<hr />
<h3 id="setTextSize">setTextSize()</h3>
<h4>Examples</h4>
<pre>
setTextSize(SMALL_FONT)
</pre>
<pre>
setTextSize(MEDIUM_FONT)
</pre>
<pre>
setTextSize(LARGE_FONT)
</pre>
<h4>Description</h4>
<p>
Sets the size of the text printed to the console in pixels. The first and only parameter is an integer that must be between between 8 and 128. The PyAngelo constants of SMALL_FONT (8), MEDIUM_FONT (16), and LARGE_FONT (24) can be used.
</p>
<h4>Syntax</h4>
<p>setTextSize(size)</p>
<h4>Parameters</h4>
<p>size - The size of the text in pixels.</p>
<hr />
<h3 id="setTextColour">setTextColour()</h3>
<h4>Examples</h4>
<pre>
setTextColour(RED)
print("I am red.")
</pre>
<h4>Description</h4>
<p>
Sets the text colour for any print statements which will be output on the console. The following constants should be used as a parameter:
</p>
<?php include __DIR__ . DIRECTORY_SEPARATOR . 'console-colours.html.php'; ?>
<h4>Syntax</h4>
<p>setTextColour()</p>
<h4>Parameters</h4>
<p>colour - An integer between 1 and 22 represeting the colour. The above constants should be used for clarity.</p>
<hr />
<h3 id="setHighlightColour">setHighlightColour()</h3>
<h4>Examples</h4>
<pre>
setTextColour(RED)
setHighlightColour(WHITE)
print("I am red text on a white background.")
</pre>
<h4>Description</h4>
<p>
Sets the highlight or background colour for any print statements which will be output on the console. The following constants should be used as a parameter:
</p>
<?php include __DIR__ . DIRECTORY_SEPARATOR . 'console-colours.html.php'; ?>
<h4>Syntax</h4>
<p>setHighlightColour()</p>
<h4>Parameters</h4>
<p>colour - An integer between 1 and 22 represeting the colour. The above constants should be used for clarity.</p>
<hr />
<h3 id="clear">clear()</h3>
<h4>Examples</h4>
<pre>
print("I am displayed but will be removed after 1 second. Help!")
sleep(1)
clear()
</pre>
<pre>
clear(RED)
</pre>
<h4>Description</h4>
<p>
Clears the console. 
</p>
<h4>Syntax</h4>
<p>clear(colour)</p>
<h4>Parameters</h4>
<p>colour -  The colour of the console after the screen is cleared. This is an optional parameter. If no colour is specified, a black colour will be displayed. The parameter is an integer from 1 to 22 but the following constants should be used for clarity:</p>
<?php include __DIR__ . DIRECTORY_SEPARATOR . 'console-colours.html.php'; ?>
<hr />

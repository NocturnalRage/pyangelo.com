<h2 id="images">Images</h2>
<h3 id="Image">Image()</h3>
<h4>Examples</h4>
<pre>
setCanvasSize(300, 300)
hero = Image("/samples/images/PyAngelo.png")
hero.draw(50, 100)
hero.dispose()
</pre>
<h4>Description</h4>
<p>
Loads an image into memory which can later be displayed with the draw(), drawRegion(), or drawFrame() methods. Use dispose() to unload the image and free its resources when no longer needed.
</p>
<h4>Methods</h4>
<h4>Image(file)</h4>
<p>file - The location of the image file to load.</p>
<h4>loadImage(file)</h4>
<p>Alias for <code>Image(file)</code>.</p>
<h4>setOpacity(alpha)</h4>
<p>alpha - Number between 0 (transparent) and 1 (opaque).</p>
<h4>setRotation(angle)</h4>
<p>angle - Rotation angle in radians (or degrees if angleMode is set to DEGREES).</p>
<h4>setScale(scaleX[, scaleY])</h4>
<p>scaleX - Horizontal scale factor.<br>
scaleY - Vertical scale factor (optional; defaults to scaleX).</p>
<h4>setFrameSize(frameW, frameH)</h4>
<p>frameW - Width of each frame in a sprite sheet.<br>
frameH - Height of each frame.</p>
<h4>setFlipX(flag)</h4>
<p>flag - 0 or false to disable horizontal flip, 1 or true to enable.</p>
<h4>setFlipY(flag)</h4>
<p>flag - 0 or false to disable vertical flip, 1 or true to enable.</p>
<h4>setPivot(ox[, oy])</h4>
<p>ox - X coordinate of the pivot point or the string "center".<br>
oy - Y coordinate of the pivot point or "center" (optional; defaults to ox).</p>
<h4>draw(x, y[, width, height])</h4>
<p>x - X coordinate where the image’s origin will be placed.<br>
 y - Y coordinate where the image’s origin will be placed.<br>
 width - Optional drawing width (defaults to image.width).<br>
 height - Optional drawing height (defaults to image.height).
</p>
<h4>drawRegion(sx, sy, sw, sh, dx, dy[, dw, dh])</h4>
<p>sx, sy - Source x and y of the sub-region.<br>
 sw, sh - Width and height of the source sub-region.<br>
 dx, dy - Destination x and y on the canvas.<br>
 dw, dh - Optional destination width and height (defaults to sw and sh).
</p>
<h4>drawFrame(index, x, y[, scaleW, scaleH])</h4>
<p>index - Frame index in the sprite sheet (0-based).<br>
 x, y - Destination coordinates on the canvas.<br>
 scaleW, scaleH - Optional scaling of the frame (defaults to frameWidth and frameHeight).</p>
<h4>dispose()</h4>
<p>Release the image’s underlying resources and remove it from memory.</p>
<h4>Properties</h4>
<p>Each image object has the following public properties:</p>
<ul>
  <li>width</li>
  <li>height</li>
  <li>file</li>
  <li>opacity</li>
  <li>rotation</li>
  <li>scale (array of [scaleX, scaleY])</li>
  <li>frameW</li>
  <li>frameH</li>
  <li>columns</li>
  <li>rows</li>
  <li>flipX</li>
  <li>flipY</li>
</ul>
<h4>More Examples</h4>
<pre>
# Example 1: Rotating and scaling an image
setCanvasSize(400, 400)
hero = Image("/samples/images/PyAngelo.png")
hero.setRotation(90)
hero.setPivot('center')
hero.setScale(1.5)
hero.draw(150, 150)
hero.dispose()

# Example 2: Drawing a sprite sheet frame
setCanvasSize(400, 400)
setImageSmoothing(False)
sprites = Image("/samples/images/alien-spritesheet.png")
sprites.setFrameSize(16, 20)
sprites.drawFrame(4, 100, 200, 160, 200)
sprites.dispose()

# Example 3: Drawing a sub-region of a larger image
setCanvasSize(400, 400)
bg = Image("/samples/endless-runner/gameOverBackground.png")
bg.drawRegion(50, 50, 100, 100, 300, 300)
bg.dispose()
</pre>
<h2 id="setImageSmoothing">setImageSmoothing()</h2>
<h3>Examples</h3>
<pre>
setImageSmoothing(False)
</pre>
<h3>Description</h3>
<p>Enables or disables image smoothing when scaling images. Pass True to enable smoothing or False for pixelated scaling.</p>
<h3>Syntax</h3>
setImageSmoothing(enabled)
<h3>Parameters</h3>
<p>enabled - True or False. Use True if you wish to enable image smoothing, use False if you want pixel scaling with no smoothing for pixel art.</p>

<h2 id="sprites">Sprites Reference</h2>
<p>To use any of the following classes, import the module:</p>
<pre><code>from sprite import *</code></pre>
<hr />

<!-- IMAGE SPRITE -->
<h3 id="Sprite">Sprite(imageSource, x=0, y=0, width=None, height=None)</h3>
<p>Loads and draws an image. Pass either a URL/path <code>str</code> or a pre-loaded image object.</p>
<h4>Key Properties</h4>
<ul>
  <li><code>imageFile</code> (read/write) – path or URL of the image</li>
  <li><code>image</code> (read/write) – the loaded image object</li>
  <li><code>x, y</code> (read/write) – position</li>
  <li><code>width, height</code> (read/write) – dimensions (clamped ≥ 0)</li>
  <li><code>opacity</code> (0.0–1.0)</li>
  <li><code>angle</code> (rotation)</li>
  <li><code>hitboxScale</code> Fractional scale of the sprite’s AABB used for collision detection (0.0 = no box, 1.0 = full image).</li>
</ul>
<h4>Core Methods</h4>
<dl>
  <dt><code>draw()</code></dt><dd>Draws the sprite at (<code>x</code>, <code>y</code>).</dd>
  <dt><code>moveBy(dx, dy)</code></dt><dd>Translate by (<code>dx</code>, <code>dy</code>).</dd>
  <dt><code>moveTo(x, y)</code></dt><dd>Set position.</dd>
  <dt><code>rotateTo(angle)</code> / <code>rotateBy(d)</code></dt><dd>Set or adjust rotation.</dd>
  <dt><code>overlaps(other)</code></dt><dd>True if bounding rectangles intersect.</dd>
  <dt><code>contains(point)</code></dt><dd>True if <code>point</code> lies inside its bounds.</dd>
  <dt><code>setHitboxScale(scale: float)</code></dt><dd>Used to reduce the hitbox of the Image, the value is clamped between 0 and 1.</dd>
</dl>
<h4>Example</h4>
<pre><code>from sprite import *
setCanvasSize(400, 300)
s1 = Sprite("/samples/images/PyAngelo.png", 50, 150)
s2 = Sprite("/samples/images/blue-alien-idle.png", 200, 150)

while True:
    background(240,240,240)
    s1.draw();  s2.draw()
    s1.moveBy(1, 0)
    if s1.overlaps(s2):
        text("Caught!", 10, 20, fontSize=24)
    sleep(1/60)
</code></pre>
<hr />

<!-- TEXT SPRITE -->
<h3 id="TextSprite">TextSprite(text, x=0, y=0, fontSize=20, fontName="Arial")</h3>
<p>Renders a line of text (or emoji) as a sprite. Automatically measures its size.</p>
<h4>Key Properties</h4>
<ul>
  <li><code>textContent</code> (read/write) – the string to display</li>
  <li><code>fontSize</code> (read/write)</li>
  <li><code>fontName</code> (read/write)</li>
  <li>Also inherits <code>x, y, opacity, angle</code></li>
</ul>
<h4>Core Methods</h4>
<dl>
  <dt><code>draw()</code></dt><dd>Draws the text at its position.</dd>
  <dt><code>setColour(r, g, b, a=None)</code></dt><dd>Sets fill colour and optional opacity.</dd>
   <dt><code>setColour(...$args)</code></dt>
+      <dd>Sets only the text‐fill colour. Accepts CSS names, hex, grey‐scale, RGB(A), array/tuple or a <code>Colour</code> object.</dd>
</dl>
<h4>Example</h4>
<pre><code>from sprite import *
setCanvasSize(400, 200)
msg = TextSprite("Hello, 🌏!", 100, 100, fontSize=32)
msg.setColour(20, 120, 200)

while True:
    background(30,30,30)
    msg.draw()
    msg.rotateBy(1)
    sleep(1/60)
</code></pre>
<hr />

<!-- RECTANGLE SPRITE -->
<h3 id="RectangleSprite">RectangleSprite(x, y, width, height)</h3>
<p>Draws a filled (and optionally stroked) rectangle.</p>
<h4>Key Properties</h4>
<ul>
  <li><code>width, height</code> (read/write)</li>
  <li>Fill/stroke via <code>setColour</code>, <code>setStroke</code>, <code>strokeWeight</code></li>
  <li>Also inherits <code>x, y, opacity, angle</code></li>
</ul>
<h4>Core Methods</h4>
<dl>
  <dt><code>setColour(...$args)</code></dt>
    <dd>Sets the interior fill colour. Accepts CSS names, hex, grey-scale, RGB(A), array/tuple or a <code>Colour</code> object.</dd>
  <dt><code>setStroke(...$args)</code></dt>
    <dd>Sets the outline stroke colour and enables the stroke. Accepts the same overloads as above.</dd>
  <dt><code>strokeWeight(w)</code></dt><dd>Border thickness.</dd>
  <dt><code>noStroke()</code></dt><dd>Disable border.</dd>
</dl>

<h4>Example</h4>
<pre><code>from sprite import *
setCanvasSize(300,300)
r = RectangleSprite(50, 50, 80, 120)
r.setColour(255,100,50)
r.setStroke(0,0,0,0.8)
r.strokeWeight(4)
while True:
    background(220)
    r.draw()
    r.moveBy(1, 1)
    if r.x + r.width > width or r.y + r.height > height:
        r.x = r.y = 0
    sleep(1/60)
</code></pre>
<hr />

<!-- CIRCLE SPRITE -->
<h3 id="CircleSprite">CircleSprite(x, y, radius)</h3>
<p>Draws a circle around its center.</p>
<h4>Key Properties</h4>
<ul>
  <li><code>radius</code> (read/write)</li>
  <li>Also inherits styling and transform props</li>
</ul>
<h4>Example</h4>
<pre><code>from sprite import *
setCanvasSize(300,300)
c = CircleSprite(150, 150, 40)
c.setColour(100,200,150)
c.noStroke()

while True:
    background(30)
    c.draw()
    c.moveBy(2, -1)
    if c.x < 0 or c.x > width or c.y < 0 or c.y > height:
        c.x = 150; c.y = 150
    sleep(1/60)
</code></pre>
<hr />

<!-- ELLIPSE SPRITE -->
<h3 id="EllipseSprite">EllipseSprite(x, y, radiusX, radiusY)</h3>
<p>Draws an ellipse around its center.</p>
<h4>Key Properties</h4>
<ul>
  <li><code>radiusX, radiusY</code> (read/write)</li>
</ul>
<h4>Example</h4>
<pre><code>from sprite import *
setCanvasSize(300,200)
e = EllipseSprite(150,100, 80,40)
e.setColour(200,50,200)
e.setStroke(255,255,255)
e.strokeWeight(2)

while True:
    background(50)
    e.draw()
    e.rotateBy(2)
    sleep(1/60)
</code></pre>
<hr />

<!-- POLYGON SPRITE -->
<h3 id="PolygonSprite">PolygonSprite(x, y, numSides=3, radius=0)</h3>
<p>Draws a regular, convex polygon.</p>
<h4>Key Properties</h4>
<ul>
  <li><code>numSides</code> (read/write, ≥ 3)</li>
  <li><code>radius</code> (read/write)</li>
</ul>
<h4>Core Methods</h4>
<dl>
  <dt><code>getVertices()</code></dt><dd>Returns a list of corner coordinates (<code>x, y</code>).</dd>
  <dt><code>getAxes()</code></dt><dd>Returns a list of normalized axes for SAT collision.</dd>
  <dt><code>project(axis)</code></dt><dd>Returns a tuple (<code>min, max</code>) of projections onto <code>axis</code>.</dd>
</dl>
<h4>Example</h4>
<pre><code>from sprite import *
setCanvasSize(400,400)
p = PolygonSprite(200,200, numSides=6, radius=80)
p.setColour(150,200,100);  p.noStroke()

# retrieve geometry for collision or custom rendering
verts = p.getVertices()
axes  = p.getAxes()
# project onto a test axis
t = p.project((1,0))  # returns (min, max)

while True:
    background(10)
    p.draw()
    p.rotateBy(1)
    sleep(1/60)
</code></pre>
<hr />
<!-- TWEENING -->
<h3 id="Tweening">Tweening</h3>
<p>All sprite instances support a built-in <code>tweenTo()</code> method to animate numeric properties over time. This is the preferred way to animate sprites.</p>
<h4>Usage</h4>
<pre><code># animate x to 200 over 2 seconds, then y to 150 over 1.5 seconds
from sprite import *
from time import time
setCanvasSize(400, 400)
r = RectangleSprite(0, 0, 50, 50)
r.tweenTo('x', 200, 2.0).onComplete(lambda: print("X done!"))
r.tweenTo('y', 150, 1.5).onComplete(lambda: print("Y done!"))

# In your main loop:
lastTime = time()
while True:
    now = time()
    dt = now - lastTime
    lastTime = now
    background(40)
    r.update(dt)   # advances all tweens registered via tweenTo
    r.draw()
    sleep(max(0, 1/60 - (time() - now)))
</code></pre>

<p>If you need to use the <code>Tween</code> class directly, you can—but for most cases <code>tweenTo()</code> on the sprite is simpler and ensures the tween is managed automatically.</p>

<h2 id="sprites">Sprites Reference</h2>
<p>Sprites are drawable objects—images, shapes, or text—that support positioning, sizing, rotation, collision, and more. All sprite types inherit shared properties and methods, while each subclass adds its own constructor signature and behavior.</p>
<p>To use any of the following classes, import the module:</p>
<pre><code>from sprite import *</code></pre>
<hr />
<h3>Common Properties & Methods</h3>
<dl>
  <dt><code>x, y</code></dt>
  <dd>Position of the sprite (interpreted according to <code>drawMode</code>).</dd>

  <dt><code>width, height</code></dt>
  <dd>Size of the sprite’s bounding box (for shapes and images; for <em>TextSprite</em>, calculated from font metrics).</dd>

  <dt><code>angle</code></dt>
  <dd>Rotation depending on angleMode for DEGREES or RADIANS. Pivot point is at (<code>anchorX</code>·<code>width</code>, <code>anchorY</code>·<code>height</code>).</dd>

  <dt><code>opacity</code></dt>
  <dd>Transparency from 0.0 (invisible) to 1.0 (opaque).</dd>

  <dt><code>drawMode</code></dt>
  <dd>Anchoring mode: <code>CORNER</code> (x, y = bottom‐left) or <code>CENTER</code> (x, y = center).</dd>

  <dt><code>anchorX, anchorY</code></dt>
  <dd>Fractional pivot for rotation and transforms: 0, 0 = left/bottom, 0.5, 0.5 = center, 1, 1 = right/top, 0, 1 = left/top, 1, 0 = right/bottom.</dd>

  <dt><code>draw()</code></dt>
  <dd>Render this sprite on the canvas.</dd>

  <dt><code>moveTo(x, y)</code></dt>
  <dd>Set the sprite’s position.</dd>

  <dt><code>moveBy(dx, dy)</code></dt>
  <dd>Offset the sprite’s position.</dd>

  <dt><code>setDrawMode(mode)</code></dt>
  <dd>Change anchoring to <code>CORNER</code> or <code>CENTER</code> for drawing and collision detection.</dd>

  <dt><code>setAnchor(xFrac, yFrac)</code></dt>
  <dd>Set both <code>anchorX</code> and <code>anchorY</code>.</dd>

  <dt><code>rotateBy(delta)</code></dt>
  <dd>Add <code>delta</code> degrees or radians to <code>angle</code>.</dd>

  <dt><code>rotateTo(angle)</code></dt>
  <dd>Set <code>angle</code> absolutely.</dd>

  <dt><code>contains(point)</code></dt>
  <dd>Return <code>True</code> if point is inside the sprite’s hitbox.</dd>

  <dt><code>overlaps(other)</code></dt>
  <dd>Return <code>True</code> if this sprite’s hitbox overlaps another.</dd>
</dl>

<hr>

<h3 id="Sprite">Sprite (Image)</h3>
<p>Base class for image sprites loaded from <code>assets/</code>.</p>
<h4>Constructor</h4>
<pre><code>Sprite(imageFile, x, y, width, height)</code></pre>

<h4>Additional Property</h4>
<dl>
  <dt><code>hitboxScale</code></dt>
  <dd>Scale factor applied to the collision box (defaults to 1.0).</dd>
</dl>

<h4>Additional Method</h4>
<dl>
  <dt><code>setHitboxScale(scale)</code></dt>
  <dd>Multiply the hitbox by <code>scale</code> before collision checks.</dd>
</dl>

<hr>

<h3 id="TextSprite">TextSprite</h3>
<p>Text label with font and textAlign support.</p>
<h4>Constructor</h4>
<pre><code>TextSprite(text, x, y, fontSize, fontName)</code></pre>
<h4>Additional Properties</h4>
<ul>
  <li><code>textContent</code> &mdash; the string being drawn</li>
  <li><code>fontSize</code> &mdash; current text size</li>
  <li><code>fontName</code> &mdash; current font family</li>
</ul>

<hr>

<h3 id="RectangleSprite">RectangleSprite</h3>
<p>Axis-aligned rectangle.</p>
<h4>Constructor</h4>
<pre><code>RectangleSprite(x, y, width, height)</code></pre>
<h4>Notes</h4>
<ul>
  <li>Defaults to <code>CORNER</code> anchoring.</li>
</ul>

<hr>

<h3 id="CircleSprite">CircleSprite</h3>
<p>Circle defined by center and radius.</p>
<h4>Constructor</h4>
<pre><code>CircleSprite(x, y, radius)</code></pre>
<h4>Notes</h4>
<ul>
  <li>Defaults to <code>CENTER</code> anchoring.</li>
</ul>
<hr>

<h3 id="EllipseSprite">EllipseSprite</h3>
<p>Ellipse defined by X/Y radii.</p>
<h4>Constructor</h4>
<pre><code>EllipseSprite(x, y, radiusX, radiusY)</code></pre>
<h4>Notes</h4>
<ul>
  <li>Defaults to <code>CENTER</code> anchoring.</li>
</ul>
<hr>

<h3 id="PolygonSprite">PolygonSprite</h3>
<p>Regular N-gon by number of sides and radius.</p>
<h4>Constructor</h4>
<pre><code>PolygonSprite(x, y, numSides, radius)</code></pre>
<h4>Notes</h4>
<ul>
  <li>Defaults to <code>CENTER</code> anchoring.</li>
</ul>
<hr>

<h3>Examples (Python Sketches)</h3>

<h4>Setup Canvas and Import</h4>
<h4>Rectangle & Polygon Together</h4>
<pre><code>from sprite import *
setCanvasSize(400, 300)

r = RectangleSprite(50, 50, 100, 60)
p = PolygonSprite(200, 150, numSides=6, radius=40)

r.draw()
p.draw()

# collision test
print(r.overlaps(p))</code></pre>

<h4>Ellipse & Circle Alignment</h4>
<pre><code>from sprite import *
setCanvasSize(400, 300)

# ellipse in CORNER mode at top-left
e = EllipseSprite(10, 10, 80, 40)
e.setDrawMode(CORNER)
e.draw()

# circle centered
c = CircleSprite(200, 150, 30)
c.draw()</code></pre>

<h4>Image and Text Combination</h4>
<pre><code>from sprite import *
setCanvasSize(500, 400)
background(40, 52, 54)
img = Sprite('/samples/images/blue-alien-idle.png', 100, 100, 128, 128)
img.draw()

textAlign(CENTER)
t = TextSprite('Welcome', 100, 200, 32)
t.setColour(255, 0, 0)
t.draw()</code></pre>

<h4>Rotating Polygon</h4>
<pre><code>from sprite import *
setCanvasSize(500, 400)
poly = PolygonSprite(250, 200, 8, 50)
poly.setColour('hotpink')

forever:
    background(40)
    poly.moveBy(1, 0)
    if poly.left > width:
        poly.x = -poly.radius
    poly.rotateBy(-1)
    poly.draw()
    strokeWeight(4)
    stroke('purple')
    line(0, 150, width, 150)
    fill('lightgrey')
    noStroke()
    textAlign(CENTER, TOP)
    text("Rotating Octagon", 250, height, 50)
    sleep(1/60)</code></pre>

<h4>Collision Detection</h4>
<pre><code>from sprite import *
setCanvasSize(300, 200)
alien = Sprite("/samples/images/blue-alien-idle.png", 150, 100)
r = RectangleSprite(150,  100,  80, 40)
r.setDrawMode(CENTER)
r.setColour('purple')

while True:
    background(40)
    if isKeyPressed(KEY_A):
        alien.moveBy(-1, 0)
    if isKeyPressed(KEY_D):
        alien.moveBy(1, 0)
    if isKeyPressed(KEY_W):
        alien.moveBy(0, 1)
    if isKeyPressed(KEY_S):
        alien.moveBy(0, -1)
    
    if alien.overlaps(r):
        fill('red')
        noStroke()
        textAlign(CENTER, BOTTOM)
        text('Overlaps', width/2, 0, 50)

    alien.draw()
    r.draw()
    sleep(1/60)</code></pre>

<h4>Collision Detection with setHitboxScale</h4>
<pre><code>
from sprite import *
setCanvasSize(640, 360)
pyangelo = Sprite("/samples/images/PyAngelo.png", 100, 75)
pyangelo.setHitboxScale(0.8)
alien = Sprite("/samples/images/blue-alien-idle.png", 300, 75)
fill(253,248,126)
noStroke()
textAlign(CENTER, TOP)
while True:
    background(167,29,239)
    pyangelo.draw()
    noFill()
    stroke(0)
    rect(pyangelo.left, pyangelo.bottom, pyangelo.width * pyangelo.hitboxScale, pyangelo.height * pyangelo.hitboxScale)
    alien.draw()
    rect(alien.left, alien.bottom, alien.width, alien.height)
    if isKeyPressed(KEY_A):
        alien.moveBy(-1, 0)
    if isKeyPressed(KEY_D):
        alien.moveBy(1, 0)
    if isKeyPressed(KEY_W):
        alien.moveBy(0, 1)
    if isKeyPressed(KEY_S):
        alien.moveBy(0, -1)
    
    if pyangelo.overlaps(alien):
        fill(253,248,126)
        noStroke()
        text("Overlapping!", width/2, height, fontSize=50)
    sleep(1/60)</code></pre>

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

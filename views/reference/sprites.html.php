<h2 id="sprites">Sprites</h2>
<h3 id="Sprite">Sprite()</h3>
<h4>Examples</h4>
<pre>
setCanvasSize(500, 400)
pyangelo = Sprite("http://www.pyangelodev.com/images/logos/pyangelo-logo.png", 100, 75)
imposter = Sprite("http://www.pyangelodev.com/images/logos/pyangelo-logo.png", 300, 75)
@loop_animation
# The code below will be repeated as it is part of the <a href="#loop_animation">@loop_animation</a>.
background(220, 220, 220)
pyangelo.draw()
imposter.draw()
pyangelo.moveBy(1, 1)
imposter.moveBy(-1, 1)

if pyangelo.overlaps(imposter):
      text("I found you imposter!", 0, 0, fontSize=30)
</pre>
<h4>Description</h4>
<p>
The Sprite class loads an image specified as the first parameter at a starting position specified by the second and third parameters. You can also specify the optional parameters of width, height, and opacity for the image.
</p>
<h4>Methods</h4>
<h5>Sprite(image, x, y, width, height, opacity)</h5>
<p>The image specifies the location on the Internet of the image to use. This is the only mandatory parameter, the rest are optional. The x and y parameters specify the starting position of the Sprite. The width and height parameters can adjust the size of the image and the opacity will change the transparency of the image.</p>
<h5>draw()</h5>
<p>Draws the Sprite's image to the canvas at the Sprite's X and Y position.</p>
<h5>moveBy(x, y)</h5>
<p>Updates the Sprite's position by x pixels along the x axis, and y pixels along the y axis.</p>
<h5>moveTo(x, y)</h5>
<p>Updates the Sprite's position to the (x, y) coordinate.</p>
<h5>overlaps(other)</h5>
<p>Returns True if this Sprite overlaps with the "other" Sprite passed in as the first parameter. If the two Sprites do not overlap then False is returned. The overlap method uses the bounding rectangle principle to check if the Sprites are overlapping.</p>
<h5>contains(point)</h5>
<p>Returns True if the point passed in as the first parameter is inside the area occupied by this Sprite, otherwise it returns False.</p>
<hr />
<h3 id="TextSprite">TextSprite()</h3>
<h4>Examples</h4>
<pre>
setCanvasSize(500, 400)
hello = TextSprite("Hello", 100, 75)
hello.setColour(255, 100, 200)
smiley = TextSprite("☺️", 300, 75, fontSize=50)
@loop_animation
# The code below will be repeated as it is part of the <a href="#loop_animation">@loop_animation</a>.
background(220, 220, 220)
hello.draw()
smiley.draw()

if hello.overlaps(smiley):
      text("Hello there good friend", 0, 0, fontSize=30)
      hello.moveBy(1, 1)
      smiley.moveBy(-1, 1)
</pre>
<h4>Description</h4>
<p>
The TextSprite class inherits from the Sprite class and so it has the same methods draw(), moveBy(), moveTo(), contains(), and overlaps(). However the first parameter passed to a TextSprite is the text to display. This text can also be an emoji. 
</p>
<h5>TextSprite(text, x, y, fontSize, fontName, r, g, b, a)</h5>
<p>The text parameter specifies what text will be displayed by the draw() method. This is the only mandatory parameter, the rest are optional. The x and y parameters specify the starting position of the Sprite. The fontSize specified the size of the text, the fontName specifies the type of font to use, and the r, g, b, and a parameters specify the colour and transparency of the text.</p>
<h4>Extra Methods</h4>
<h5>setColour(r, g, b, a)</h5>
<p>This method sets the colour and transparency of the text.</p>
<hr />
<h3 id="RectangleSprite">RectangleSprite()</h3>
<h4>Examples</h4>
<pre>
setCanvasSize(500, 400)
r = RectangleSprite(100, 100, 50, 50)
r.setColour(255, 10, 170)
r.noStroke()
xSpeed = 2
ySpeed = 2

@loop_animation
background(220, 220, 220)
r.draw()
r.moveBy(xSpeed, ySpeed)
if r.x >= width - r.width:
    r.x = width - r.width
    xSpeed *= -1
elif r.x <= 0:
    r.x = 0
    xSpeed *= -1
if r.y >= height - r.height:
    r.y = height - r.height
    ySpeed *= -1
elif r.y <= 0:
    r.y = 0
    ySpeed *= -1
</pre>
<h4>Description</h4>
<p>
The RectangleSprite class inherits from the TextSprite class and so it has the same methods draw(), setColour(), moveBy(), moveTo(), contains(), and overlaps(). However the first four parameter passed to a RectangleSprite are the x and y coordintates and the width and height of the rectangle.
</p>
<h5>RectangleSprite(x, y, width, height, r, g, b, a)</h5>
<p>The first four parameter specify the x and y coordinates and the width and height of the rectangle. The first four parameters are mandatory, the rest are optional. The r, g, b, and a parameters specify the colour and transparency of the rectangle.</p>
<h4>Extra Methods</h4>
<h5>noStroke()</h5>
<p>This updates the rectangle so no border will be drawn when the draw() method is called.</p>
<h5>stroke(r, g, b, a)</h5>
<p>This method ensures the rectangle is drawn with a border of the colour and transparency specified by the r, g, b, and a parameters.</p>
<h5>strokeWeight(weight)</h5>
<p>Specifies in pixels, how thick the border should be around the rectangle.</p>
<hr />
<h3 id="CircleSprite">CircleSprite()</h3>
<h4>Examples</h4>
<pre>
setCanvasSize(500, 400)
c = CircleSprite(100, 100, 50)
c.setColour(255, 10, 170)
c.noStroke()
xSpeed = 2
ySpeed = 2

@loop_animation
background(100, 100, 200)
c.draw()
c.moveBy(xSpeed, ySpeed)
if c.x >= width - c.radius:
    c.x = width - c.radius
    xSpeed *= -1
elif c.x <= c.radius:
    c.x = c.radius
    xSpeed *= -1
if c.y >= height - c.radius:
    c.y = height - c.radius
    ySpeed *= -1
elif c.y <= c.radius:
    c.y = c.radius
    ySpeed *= -1
</pre>
<h4>Description</h4>
<p>
The CircleSprite class inherits from the RectangleSprite class and so it has the same methods draw(), stroke(), noStroke(), strokeWeight(), setColour(), moveBy(), moveTo(), contains(), and overlaps(). However the first three parameter passed to a CircleSprite are the x and y coordintates of the center of the circle, and the radius of the circle.
</p>
<h5>CircleSprite(x, y, radius, r, g, b, a)</h5>
<p>The first three parameter specify the x and y coordinates of the center of the circle and the radius of the circle. The first three parameters are mandatory, the rest are optional. The r, g, b, and a parameters specify the colour and transparency of the rectangle.</p>
<hr />
<h3 id="EllipseSprite">EllipseSprite()</h3>
<h4>Examples</h4>
<pre>
setCanvasSize(500, 400)
e = EllipseSprite(100, 100, 50, 25)
e.setColour(255, 10, 170)
e.stroke(255, 0, 0)
xSpeed = 2
ySpeed = 2

@loop_animation
background(100, 100, 200)
e.draw()
e.moveBy(xSpeed, ySpeed)
if e.x >= width - e.radiusX:
    e.x = width - e.radiusX
    xSpeed *= -1
elif e.x <= e.radiusX:
    e.x = e.radiusX
    xSpeed *= -1
if e.y >= height - e.radiusY:
    e.y = height - e.radiusY
    ySpeed *= -1
elif e.y <= e.radiusY:
    e.y = e.radiusY
    ySpeed *= -1
</pre>
<h4>Description</h4>
<p>
The EllipseSprite class inherits from the RectangleSprite class and so it has the same methods draw(), stroke(), noStroke(), strokeWeight(), setColour(), moveBy(), moveTo(), contains(), and overlaps(). However the first four parameter passed to a EllipseSprite are the x and y coordintates of the center of the circle, and the X radius and Y radius of the ellipse.
</p>
<h5>EllipseSprite(x, y, radiusX, radiusY, r, g, b, a)</h5>
<p>The first four parameter specify the x and y coordinates of the center of the ellipse and the X radius and Y radius of the ellipse. The first four parameters are mandatory, the rest are optional. The r, g, b, and a parameters specify the colour and transparency of the rectangle.</p>
<hr />

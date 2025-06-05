<h2 id="images">Images</h2>
<h3 id="Image">Image()</h3>
<h4>Examples</h4>
<pre>
setCanvasSize(300, 300)
hero = Image("/samples/images/PyAngelo.png")
hero.draw(50, 100)
</pre>
<h4>Description</h4>
<p>
Loads an image into memory which can later be displayed with the draw() method, or drawImage() function.
</p>
<h4>Methods</h4>
<h4>Image(file)</h4>
<p>file - The location of the image file to load.</p>
<h4>draw(x, y, width, height, opacity)</h4>
<h4>Parameters</h4>
<p>x - The x coordinate of the image.</p>
<p>y - The y coordinate of the image.</p>
<p>width - How wide to draw the image in pixels.</p>
<p>height - How high to draw the image in pixels.</p>
<p>opacity - Changes the transparency of the image from 0 (full opacity) to 1 (no opacity).</p>
<p>Draws the image associated with this object when created. The first parameter is the x position and the second parameter is the y position of where to draw the image. The x and y position refers to the bottom left of the image. The third parameter is the width of the image and is optional. If this is not passed in the actual width of the image is used. This can be used to scale the image. The fourth parameter is the height of the image and is optional. If this is not passed in the actual height of the image is used. This can be used to scale the image. The fifth parameter is the opacity of the image specified by a number from 0 to 1 where 0 is full opacity and 1 is no opacity. If you wish to only specify the opacity without specifying a width and height you can use named parameters.</p>
<h4>Properties</h4>
<p>Each image object has the following public properties:</p>
<ul>
  <li>width</li>
  <li>height</li>
  <li>file</li>
</ul>
<hr />
<h3 id="drawImage">drawImage()</h3>
<h4>Examples</h4>
<pre>
# Draw PyAngelo at (0, 0)
setCanvasSize(350, 300)
hero = Image("/samples/images/PyAngelo.png")
drawImage(hero, 0, 0)

# Draw PyAngelo at (128, 96) with a width of 64 and height of 48 pixels
smallHero = Image("/samples/images/PyAngelo.png")
drawImage(smallHero, 128, 96, 64, 48)

# Draw PyAngelo at (194, 144) with an opacity of 0.2
opacityHero = Image("/samples/images/PyAngelo.png")
drawImage(opacityHero, 194, 144, opacity=0.2)
</pre>
<h4>Description</h4>
<p>
Draws an image to the canvas that has previously been loaded by creating an image object via the Image() class. The first parameter is the image object created using the Image() class. The second parameter is the x position and the third parameter is the y position to draw the image. The x and y position refers to the top left of the image. The fourth parameter is the width of the image and is optional. If this is not passed in the actual width of the image is used. This can be used to scale the image. The fifth parameter is the height of the image and is optional. If this is not passed in the actual height of the image is used. This can be used to scale the image. The sixth parameter is the opacity of the image specified by a number from 0 to 1 where 0 is full opacity and 1 is no opacity. If you wish to only specify the opacity with specifying a width and height you can use named parameters as shown in the example above.
</p>
<h4>Syntax</h4>
<p>drawImage(image, x, y, width, height, opacity)</p>
<h4>Parameters</h4>
<p>image - The loaded image to be drawn. This image is an object created via the Image() class.</p>
<p>x - The x coordinate of the image.</p>
<p>y - The y coordinate of the image.</p>
<p>width - How wide to draw the image in pixels.</p>
<p>height - How high to draw the image in pixels.</p>
<p>opacity - Changes the transparency of the image from 0 (full opacity) to 1 (no opacity).</p>
<hr />

<h2 id="keyboard">Keyboard</h2>
<h3 id="isKeyPressed">isKeyPressed()</h3>
<h4>Examples</h4>
<pre>
pyangelo = loadImage("/samples/images/PyAngelo.png")
setCanvasSize(640, 360)
x = width/2 - 64
y = height/2 - 48
while True:
    background(255, 255, 0)
    image(pyangelo, x, y)
    text("Use the WASD keys to move me around!", 20, 30)
    if isKeyPressed(KEY_W):
        y -= 1
    if isKeyPressed(KEY_A):
        x -= 1
    if isKeyPressed(KEY_S):
        y += 1
    if isKeyPressed(KEY_D):
        x += 1
    sleep(0.005)
</pre>
<h4>Description</h4>
<p>
Returns True if the key specified by the first parameter is currently pressed, otherwise returns False. Once the key has been pressed the function will continue to return True each time it is called until the key is released. There is a <a href="#keys">list of constants</a> that can be used in PyAngelo to represent each key for clarity.
</p>
<h4>Syntax</h4>
<p>isKeyPressed(code)</p>
<h4>Parameters</h4>
<p>code - The code representing a key on the keyboard. See a <a href="https://developer.mozilla.org/en-US/docs/Web/API/KeyboardEvent/code/code_values">list of codes.</a></p>
<hr />
<h3 id="wasKeyPressed">wasKeyPressed()</h3>
<h4>Examples</h4>
<pre>
pyangelo = loadImage("/samples/images/PyAngelo.png")
setCanvasSize(640, 360)
x = width/2 - 64
y = height/2 - 48
while True:
    background(255, 255, 0)
    image(pyangelo, x, y)
    text("Use the WASD keys to move me around!", 20, 30)
    if wasKeyPressed(KEY_W):
        y -= 1
    if wasKeyPressed(KEY_A):
        x -= 1
    if wasKeyPressed(KEY_S):
        y += 1
    if wasKeyPressed(KEY_D):
        x += 1
    sleep(0.005)
</pre>
<h4>Description</h4>
<p>
Returns True if the key specified by the first parameter has been pressed and not yet released before this function is called. Otherwise, it returns False. Once the key has been pressed and the function has been called, the function will then return False until the key is either pressed again, or if it is being held down the operating system sends another keypressed event. This is different from the isKeyPressed() function which continues to return True when called until the key is released. There is a <a href="#keys">list of constants</a> that can be used in PyAngelo to represent each key for clarity.
</p>
<h4>Syntax</h4>
<p>wasKeyPressed(code)</p>
<h4>Parameters</h4>
<p>code - The code representing a key on the keyboard. See a <a href="https://developer.mozilla.org/en-US/docs/Web/API/KeyboardEvent/code/code_values">list of codes.</a></p>
<hr />

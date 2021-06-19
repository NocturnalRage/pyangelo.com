<h2 id="animation">Animation</h2>
<h3 id="sleep">sleep()</h3>
<h4>Examples</h4>
<pre>
setCanvasSize(640, 360)
x = 0
radius = 50
sleepSeconds = 0.005

noStroke()
fill(255, 255, 0, 0.7)
while True:
    background(0, 0, 0)
    circle(x, 180, radius)
    x += 1
    if x > width + radius:
        x = -radius
    sleep(sleepSeconds)
</pre>
<h4>Description</h4>
<p>
The sleep() function causes the program to suspend for the number of seconds specified by the first parameter. This is extremely useful for animation purposes inside a while loop as shown in the example above. Try changing some of the variables in the program to see the effect it has.
</p>
<h4>Syntax</h4>
<p>sleep(delay)</p>
<h4>Parameters</h4>
<p>delay - The number of seconds to delay the program for.</p>
<hr />

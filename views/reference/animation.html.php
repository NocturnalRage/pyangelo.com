<h2 id="animation">Animation</h2>
<h3 id="sleep">Controlling Frame Rate with sleep()</h3>
<p>In simple loops, you can throttle to a fixed FPS by using sleep()</p>
<h4>Example</h4>
<pre><code>
setCanvasSize(640, 360)
x = 0
radius = 50

noStroke()
fill(255, 255, 0, 0.7)
while True:
    background(0, 0, 0)
    circle(x, 180, radius)
    x += 1
    if x > width + radius:
        x = -radius
    sleep(1/60)
</code></pre>
<h4>Description</h4>
<p>
The sleep() function causes the program to suspend for the number of seconds specified by the first parameter. This is extremely useful for animation purposes inside a while loop as shown in the example above. Try changing some of the variables in the program to see the effect it has.
</p>
<h4>Syntax</h4>
<p>sleep(delay)</p>
<h4>Parameters</h4>
<p>delay - The number of seconds to delay the program for.</p>
<hr />
<h2 id="deltaTimer">Introducing DeltaTimer</h2>
<p>DeltaTimer automatically measures the true time between frames (dt) and lets you both throttle to a target FPS and query the current or smoothed frame rate.</p>
<pre><code>from deltaTimer import DeltaTimer</code></pre>
<h3>Creating a Timer</h3>
<pre><code># Target 60 FPS, smooth FPS reading over last 30 frames
timer = DeltaTimer(fps=60, smoothing=30)
</code></pre>
<p>
<ul>
  <li><strong>fps:</strong> frames per second you want to target (default: 60)</li>
  <li><strong>smoothing:</strong> number of frames over which to average your FPS reading (default: 30)</li>
</ul>
</p>
<h4>Example</h4>
<pre><code>from deltaTimer import DeltaTimer
timer = DeltaTimer()

setCanvasSize(640, 360)
x = 0
radius = 50
# Ball speed in pixels per second
# It should take 2 seconds to go across the screen
SPEED = 320

noStroke()
fill(255, 255, 0, 0.7)
while True:
    dt = timer.update()
    background(0, 0, 0)
    circle(x, 180, radius)
    x += SPEED * dt
    if x > width + radius:
        x = -radius
    fill(0, 255, 0)
    fps = round(timer.getFps())
    text("FPS: " + str(fps), 10, height - 50, 50)
    timer.enforceFps()

</code></pre>
<h3>DeltaTimer API Reference</h3>
<h4>timer = DeltaTimer(self, fps=60, smoothing=30)</h4>
<p>
<ul>
<li>fps (int): Desired frames per second for throttling and target timing.</li>
<li>smoothing (int): Number of most recent frames over which to average FPS in getFps(averaged=True).</li>
</ul>
</p>
<h3>update()</h3>
<p>Call at the start of each frame/iteration.</p>
<p>Returns dt (float): elapsed seconds since the last call to update() (or since creation/resume).</p>
<p>If the timer is paused, returns 0.0 and does not update the internal clock.</p>
<h3>enforceFps()</h3>
<p>Call at the end of each frame/iteration.</p>
<p>Sleeps for the remainder of the frame budget (1/fps - work_time).</p>
<p>If the frame work took longer than the budget, returns immediately (no negative sleep).</p>
<p>If paused, still sleeps exactly one frame’s worth (1/fps).</p>
<h3>getFps(averaged=True)</h3>
<p>
<ul>
<li>averaged: when True, returns 1.0 / (mean dt over last N frames); when False, returns 1.0 / last_dt.</li>
<li>Returns fps (float): your instantaneous or smoothed frames-per-second.  Returns 0.0 if dt was 0.0 (paused or no movement).</li>
</ul>
</p>
<h3>pause()</h3>
<p>Pauses the timer. Subsequent update() calls return 0.0 until resume() is called.</p>
<h3>resume()</h3>
<p>Resumes timing, resetting the internal last-time so that next update() has no large jump.</p>
<h3>DeltaTimer Summary</h3>
<p>With DeltaTimer, you get both accurate motion based on real elapsed time and smooth, reliable FPS throttling—without re-writing your loop logic or manually calculating sleeps.</p>

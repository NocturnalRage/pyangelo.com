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
<h2 id="simpleTimer">Introducing SimpleTimer</h2>
<p>SimpleTimer is a fixed timestep frame capping timer. Use this when you are running logic at a fixed rate (eg 60FPS) and <strong>not</strong> using dt.</p>
<pre><code>from gameLoop import SimpleTimer</code></pre>
<h3>Creating a Timer</h3>
<pre><code># Target 60 FPS
timer = SimpleTimer(60)
</code></pre>
<p>
<ul>
  <li><strong>fps:</strong> frames per second you want to target (default: 60)</li>
</ul>
</p>
<h4>Example</h4>
<pre><code>from gameLoop import SimpleTimer
timer = SimpleTimer()  # default fps=60 → dt = 1/60

setCanvasSize(600, 360)
x = 0
radius = 50

# Ball speed in pixels per second
# It should take 2 seconds to go across the screen
SPEED = 5 # Should move 5 * 60 = 300 px per second

noStroke()
fill(255, 255, 0)

while True:
    timer.update()
    background(0, 0, 0)
    circle(x, 180, radius)
    x += SPEED
    if x > width + radius:
        x = -radius

    fill(0, 255, 0)
    text("FPS: 60", 10, height - 50, 50)  # Fixed frame rate

    timer.enforceFps()
</code></pre>
<h3>SimpleTimer API Reference</h3>
<h4>timer = SimpleTimer(self, fps=60)</h4>
<p>
<ul>
<li>fps (int): Desired frames per second for throttling and target timing.</li>
</ul>
</p>
<h3>update()</h3>
<p>Call at the start of each frame/iteration.</p>
<h3>enforceFps()</h3>
<p>Call at the end of each frame/iteration.</p>
<p>Sleeps for the remainder of the frame budget (1/fps - work_time).</p>
<p>If the frame work took longer than the budget, returns immediately (no negative sleep).</p>
<h3>reset()</h3>
<p>Restarts the internal clock so your next <code>update()</code> is “fresh.”</p>
<h3>tick()</h3>
<p>A convenience that combines <code>update()</code> then <code>enforceFps()</code> in one call.</p>
<h3>setFps(fps)</h3>
<p>Change your target FPS.  Throws <code>ValueError</code> if <code>fps</code> ≤ 0.</p>
<h3>SimpleTimer Summary</h3>
<p>With SimpleTimer, you cap the maximum frame rate ensuring the game loop runs at the same speed on all computers, unless the game loop cannot be completed within the target frame rate in which case the game will slow down.</p>

<h2 id="deltaTimer">Introducing DeltaTimer</h2>
<p>DeltaTimer automatically measures the true time between frames (dt) and lets you both throttle to a target FPS and query the current or smoothed frame rate.</p>
<pre><code>from gameLoop import DeltaTimer</code></pre>
<h3>Creating a Timer</h3>
<pre><code># Target 60 FPS, smooth FPS reading over last 30 frames
timer = DeltaTimer(fps=60, smoothing=30, max_dt_multiplier=4)
</code></pre>
<p>
<ul>
  <li><strong>fps:</strong> frames per second you want to target (default: 60)</li>
  <li><strong>smoothing:</strong> number of frames over which to average your FPS reading (default: 30)</li>
  <li><strong>max_dt_multiplier:</strong> limits the max dt returned to be a multiple of the target frame rate</li>
</ul>
</p>
<h4>Example</h4>
<pre><code>from gameLoop import DeltaTimer
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
<h4>timer = DeltaTimer(self, fps=60, smoothing=30, max_dt_multiplier=4)</h4>
<p>
<ul>
<li>fps (int): Desired frames per second for throttling and target timing.</li>
<li>smoothing (int): Number of most recent frames over which to average FPS in getFps(averaged=True).</li>
<li>max_dt_multiplier (float): optional clamp on any large frame‐deltas.  Any raw ⏱ dt &gt; (1/fps × max_dt_multiplier) is capped to that value.</li>
</ul>
</p>
<h3>update()</h3>
<p>Call at the start of each frame/iteration.</p>
<p>Returns dt (float): elapsed seconds since the last call to update() (or since creation/resume).</p>
<p>If the timer is paused, returns 0.0 and does not update the internal clock.</p>
<p>If you provided a max_dt_multiplier, any raw dt above (1/fps × max_dt_multiplier) is clamped to that ceiling to protect against huge frame jumps.</p>
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
<h3>reset()</h3>
<p>Clears the history buffer, resets <code>last_dt</code> to the target frame time, wipes any pending <code>frame_start</code>, and restarts the internal clock so your next <code>update()</code> is “fresh.”</p>
<h3>tick()</h3>
<p>A convenience that combines <code>enforceFps()</code> then <code>update()</code>, returning the (possibly clamped) <em>dt</em> in one call.</p>
<h3>setFps(fps, max_dt_multiplier=None)</h3>
<p>Change your target FPS (and optionally update your clamp multiplier).  Throws <code>ValueError</code> if <code>fps</code> or <code>max_dt_multiplier</code> ≤ 0.</p>
<h3>Properties</h3>
<ul>
  <li><strong>timer.dt_history</strong> – a list copy of your last N frame-deltas (useful for custom graphs).</li>
  <li><strong>timer.last_dt</strong> – the raw (or clamped) delta from the most recent <code>update()</code>.</li>
  <li><strong>timer.fps</strong> – shorthand for <code>timer.getFps()</code>, averaging over history if available.</li>
</ul>
<h3>DeltaTimer Summary</h3>
<p>With DeltaTimer, you get both accurate motion based on real elapsed time and smooth, reliable FPS throttling—without re-writing your loop logic or manually calculating sleeps.</p>
<h4>Another Example</h4>
<pre><code>
from gameLoop import DeltaTimer

# 1) Create the timer (60 FPS target, 30-frame smoothing, clamp any dt > 4× frame time)
timer = DeltaTimer(fps=60, smoothing=30, max_dt_multiplier=4)

# 2) A simple x-position to animate
x = 0

setCanvasSize(600, 480)

while True:
    # — throttle + get dt —
    dt = timer.tick()

    # — move at 100 px/sec (clamped if dt spikes) —
    x += 100 * dt
    if x > width:
        x = 0

    if isKeyPressed(KEY_P):
        timer.pause()
    # R to resume (no giant jump on next dt)
    elif isKeyPressed(KEY_R):
        timer.resume()
    # T to reset history & clock
    elif isKeyPressed(KEY_T):
        timer.reset()

    # — render —
    background(240, 240, 240)
    fill(50, 150, 250)
    rect(x, height/2 - 25, 50, 50)

    # — overlay the smoothed FPS —
    fill(0)
    text(f"FPS: {timer.fps:.1f}", 10, 30)
</code></pre>
<p>You should see a blue square gliding smoothly across the canvas at ~100 px/s. The FPS readout in the top-left reflects your smoothed frame-rate. Tab away or minimise for a bit, then come back—notice the square “jumps” (due to clamping) but your FPS display stays bounded. Hit P to pause (the square stops and FPS goes to 0), R to resume, and T to reset the internal history/clock.</p>

<h2 id="sounds">Sound</h2>

<h3 id="Sound">Sound(filename: str) → Sound</h3>
<h4>Examples</h4>
<pre>
# Load a sound file and keep the object
music = Sound("/samples/music/Myth.mp3")

# Play, pause, and stop control:
music.play()
music.pause()
music.stop()
</pre>
<h4>Description</h4>
<p>
Creates a new <code>Sound</code> instance for the given file URL.  You can then call methods on that instance to control playback, volume, looping, etc.
</p>
<h4>Parameters</h4>
<ul>
  <li><code>filename</code> – A string URL to the sound file.</li>
</ul>
<hr/>

<h3 id="play">play() → None</h3>
<h4>Examples</h4>
<pre>
sound.play()          # start or resume playback
</pre>
<h4>Description</h4>
<p>
Starts or resumes the sound.  If the sound was paused, it picks up from where it left off; otherwise it begins at the current seek position (default 0.0).
</p>
<hr/>

<h3 id="pause">pause() → None</h3>
<h4>Examples</h4>
<pre>
sound.pause()         # temporarily halt playback
</pre>
<h4>Description</h4>
<p>
Pauses the sound at its current position.  A subsequent <code>play()</code> will resume from this point.
</p>
<hr/>

<h3 id="stop">stop() → None</h3>
<h4>Examples</h4>
<pre>
sound.stop()          # halt and reset position to 0.0
</pre>
<h4>Description</h4>
<p>
Stops playback and resets the playhead to the start (0.0 seconds).  Loop and volume settings remain unchanged.
</p>
<hr/>

<h3 id="isPlaying">isPlaying() → bool</h3>
<h4>Description</h4>
<p>
Returns <code>True</code> if the sound is currently playing (not paused), otherwise <code>False</code>.
</p>
<hr/>

<h3 id="seek">seek(position: float=None) → float or None</h3>
<h4>Description</h4>
<p>
Without arguments, returns the current playhead position in seconds (0.0 before play).  With a <code>position</code> argument (in seconds), jumps to that time and returns <code>None</code>.
</p>
<hr/>

<h3 id="rate">rate(speed: float=None) → float or None</h3>
<h4>Description</h4>
<p>
When called without arguments, returns the current playback rate (a multiplier, default 1.0).  With <code>speed</code> &gt; 0, sets a new rate (e.g. 2.0 for double speed) and returns <code>None</code>.
</p>
<hr/>

<h3 id="volume">volume(level: float=None) → float or None</h3>
<h4>Description</h4>
<p>
Without arguments, returns the current volume (0.0–1.0).  With <code>level</code> between 0.0 and 1.0, sets the volume and returns <code>None</code>.
</p>
<hr/>

<h3 id="loop">loop(state: bool=None) → bool or None</h3>
<h4>Description</h4>
<p>
With no arguments, returns whether looping is currently enabled.  With <code>state</code>=<code>True</code> or <code>False</code>, toggles looping and returns <code>None</code>.
</p>
<hr/>

<h3 id="mute">mute(state: bool=None) → bool or None</h3>
<h4>Description</h4>
<p>
Without arguments, returns current mute state.  With a boolean <code>state</code>, mutes or unmutes the sound.
</p>
<hr/>

<h3 id="fade">fade(from: float, to: float, duration: float) → None</h3>
<h4>Description</h4>
<p>
Fade volume from <code>from</code>→<code>to</code> (both 0.0–1.0) over <code>duration</code> seconds.
</p>
<hr/>

<h3 id="duration">duration() → float</h3>
<h4>Description</h4>
<p>
Returns the total length of the sound in seconds.
</p>
<hr/>

<h3 id="dispose">dispose() → None</h3>
<h4>Description</h4>
<p>
Stops playback (if any), unloads the audio from memory, and removes the instance from internal registries.
</p>
<hr/>

<h3 id="stopAll">stopAll() → None</h3>
<h4>Examples</h4>
<pre>
# static call on the class:
Sound.stopAll()

# or from an instance:
music.stopAll()
</pre>
<h4>Description</h4>
<p>
Stops every <code>Sound</code> instance currently registered, no matter what file they’re playing.
</p>
<hr/>

<h3 id="examples">Usage Example</h3>
<pre>
# Create and configure a Sound
music = Sound("/samples/music/Myth.mp3")
music.volume(0.5)        # half volume
music.loop(True)         # repeat forever
music.play()

# …later, pause and inspect…
music.pause()
print("At", music.seek(), "seconds")

# and finally stop everything
Sound.stopAll()
</pre>
<hr/>

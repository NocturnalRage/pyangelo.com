<h2 id="sounds">Sounds</h2>
<h3 id="loadSound">loadSound()</h3>
<h4>Examples</h4>
<pre>
blip = loadSound("/samples/sounds/blip.wav")
</pre>
<h4>Description</h4>
<p>
loadSound loads a sound file that can be played with the <a href="#playSound()">playSound()</a> function.
</p>
<h4>Syntax</h4>
<p>loadSound(filename)</p>
<h4>Parameters</h4>
<p>filename - A URL specifying the location of the sound file to load.</p>
<h4>Return Values</h4>
The filename is returned and can be stored in a variable. This variable can then be passed to the <a href="#playSound">playSound()</a> function in order to play the sound.
<hr />
<h3 id="playSound">playSound()</h3>
<h4>Examples</h4>
<pre>
setCanvasSize(500, 400)
blip = loadSound("/samples/sounds/blip.wav")

text("Hit W to make a 'blip' sound!", 0, 0)
while True:
    if isKeyPressed(KEY_W):
        playSound(blip)
</pre>
<h4>Description</h4>
<p>
playSound() plays a sound. The sound can be loaded previously via the <a href="#loadSound">loadSound()</a> function or can be the location of a sound file. You can also specify the optional parameters of loop and volume.
</p>
<h4>Syntax</h4>
<p>playSound(sound, loop, volume)</p>
<h4>Parameters</h4>
<p>sound - Either the name of a variable returned from the of the <a href="#loadSound">loadSound()</a> function  or the URL specifying the loaction of a sound file.</p>
<p>loop - A boolean value specifying if the sound should loop when played.</p>
<p>volume - The volume at which to play the sound ranging from 0 to 1.</p>
<hr />
<h3 id="stopSound">stopSound()</h3>
<h4>Examples</h4>
<pre>
setCanvasSize(500, 400)
music = loadSound("/samples/music/Myth.mp3")
playSound(music)

text("Hit W to stop the music!", 0, 0)
while True:
    if isKeyPressed(KEY_W):
        stopSound(music)
</pre>
<h4>Description</h4>
<p>
stopSound() stops a sound from playing.
</p>
<h4>Syntax</h4>
<p>stopSound(sound)</p>
<h4>Parameters</h4>
<p>sound - The name of a sound that has been previously played.</p>
<hr />
<h3 id="pauseSound">pauseSound()</h3>
<h4>Examples</h4>
<pre>
setCanvasSize(500, 400)
music = loadSound("/samples/music/Myth.mp3")
playSound(music)
playing = True

text("Hit W to pause the music!", 0, 0)
text("Hit S to re-start the music!", 0, 30)
while True:
    if isKeyPressed(KEY_W):
        playing = False
        pauseSound(music)
    elif isKeyPressed(KEY_S) and not playing:
        playSound(music)
        playing = True
</pre>
<h4>Description</h4>
<p>
pauseSound() pauses a sound from playing.
</p>
<h4>Syntax</h4>
<p>pauseSound(sound)</p>
<h4>Parameters</h4>
<p>sound - The name of a sound that has been previously played.</p>
<hr />
<h3 id="stopAllSounds">stopAllSounds()</h3>
<h4>Examples</h4>
<pre>
setCanvasSize(50, 50)
music1 = loadSound("/samples/music/Myth.mp3")
music2 = loadSound("/samples/music/SuperMonaco.mp3")
playSound(music1)
playSound(music2)
sleep(3)
stopAllSounds()
</pre>
<h4>Description</h4>
<p>
stopAllSounds() stops all sounds from playing.
</p>
<h4>Syntax</h4>
<p>stopAllSounds()</p>
<hr />

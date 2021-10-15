<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1>Asset Library</h1>
        <p>The assets below can be used in your programs without needing to upload them to your sketch. Simply copy the path from below and include it as if you had uploaded the asset.</p>
      </div><!-- col-md-12 -->
    </div><!-- row -->
    <div class="row">
      <?php
        include __DIR__ . DIRECTORY_SEPARATOR . 'asset-library-menu.html.php';
      ?>
      <div class="col-md-9">
        <h2 id="images">Images</h2>
        <img src ="/samples/images/PyAngelo.png" alt="PyAngelo" />
        <pre>/samples/images/PyAngelo.png</pre>
        <img src ="/samples/images/blue-alien-idle.png" alt="Idle Blue Alien" />
        <pre>/samples/images/blue-alien-idle.png</pre>
        <hr />
        <h2 id="sounds">Sounds</h2>
        <figure>
          <figcaption>Blip</figcaption>
            <audio controls preload="none" src="/samples/sounds/blip.wav">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/sounds/blip.wav</pre>
        <figure>
          <figcaption>Collision</figcaption>
            <audio controls preload="none" src="/samples/sounds/collision.wav">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/sounds/collision.wav</pre>
        <figure>
          <figcaption>Death</figcaption>
            <audio controls preload="none" src="/samples/sounds/death.wav">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/sounds/death.wav</pre>
        <figure>
          <figcaption>Hit</figcaption>
            <audio controls preload="none" src="/samples/sounds/hit.wav">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/sounds/hit.wav</pre>
        <figure>
          <figcaption>Hit 2</figcaption>
            <audio controls preload="none" src="/samples/sounds/hit2.wav">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/sounds/hit2.wav</pre>
        <figure>
          <figcaption>Hit 3</figcaption>
            <audio controls preload="none" src="/samples/sounds/hit3.wav">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/sounds/hit3.wav</pre>
        <figure>
          <figcaption>Jump</figcaption>
            <audio controls preload="none" src="/samples/sounds/jump.wav">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/sounds/jump.wav</pre>
        <figure>
          <figcaption>Pickup</figcaption>
            <audio controls preload="none" src="/samples/sounds/pickup.wav">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/sounds/pickup.wav</pre>
        <figure>
          <figcaption>Power Up</figcaption>
            <audio controls preload="none" src="/samples/sounds/powerup.wav">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/sounds/powerup.wav</pre>
        <figure>
          <figcaption>Power Up 2</figcaption>
            <audio controls preload="none" src="/samples/sounds/powerup2.wav">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/sounds/powerup2.wav</pre>
        <figure>
          <figcaption>Shoot</figcaption>
            <audio controls preload="none" src="/samples/sounds/shoot.wav">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/sounds/shoot.wav</pre>
        <figure>
          <figcaption>Shoot 2</figcaption>
            <audio controls preload="none" src="/samples/sounds/shoot2.wav">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/sounds/shoot2.wav</pre>
        <figure>
          <figcaption>Correct</figcaption>
            <audio controls preload="none" src="/samples/sounds/correct.mp3">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/sounds/correct.mp3</pre>
        <hr />
        <h2 id="music">Music</h2>
        <figure>
          <figcaption>After Burner</figcaption>
            <audio controls preload="none" src="/samples/music/Afterburner_01.mp3">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/music/Afterburner_01.mp3</pre>
        <figure>
          <figcaption>Cybernoid II</figcaption>
            <audio controls preload="none" src="/samples/music/Cybernoid_II.mp3">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/music/Cybernoid_II.mp3</pre>
        <figure>
          <figcaption>Eliminator End</figcaption>
            <audio controls preload="none" src="/samples/music/Eliminator_end.mp3">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/music/Eliminator_end.mp3</pre>
        <figure>
          <figcaption>Eliminator Intro</figcaption>
            <audio controls preload="none" src="/samples/music/Eliminator_intro.mp3">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/music/Eliminator_intro.mp3</pre>
        <figure>
          <figcaption>Golden Axe</figcaption>
            <audio controls preload="none" src="/samples/music/Golden_Axe.mp3">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/music/Golden_Axe.mp3</pre>
        <figure>
          <figcaption>Golden Axe Ending</figcaption>
            <audio controls preload="none" src="/samples/music/Golden_Axe_ending.mp3">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/music/Golden_Axe_ending.mp3</pre>
        <figure>
          <figcaption>Golden Axe Game</figcaption>
            <audio controls preload="none" src="/samples/music/Golden_Axe_game.mp3">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/music/Golden_Axe_game.mp3</pre>
        <figure>
          <figcaption>Golden Axe Selection</figcaption>
            <audio controls preload="none" src="/samples/music/Golden_Axe_selection.mp3">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/music/Golden_Axe_selection.mp3</pre>
        <figure>
          <figcaption>Lemmings 1</figcaption>
            <audio controls preload="none" src="/samples/music/Lemmings_01.mp3">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/music/Lemmings_01.mp3</pre>
        <figure>
          <figcaption>Lemmings 2</figcaption>
            <audio controls preload="none" src="/samples/music/Lemmings_02.mp3">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/music/Lemmings_02.mp3</pre>
        <figure>
          <figcaption>Myth</figcaption>
            <audio controls preload="none" src="/samples/music/Myth.mp3">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/music/Myth.mp3</pre>
        <figure>
          <figcaption>Robo Cop</figcaption>
            <audio controls preload="none" src="/samples/music/RoboCop_3.mp3">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/music/RoboCop_3.mp3</pre>
        <figure>
          <figcaption>Super Monaco</figcaption>
            <audio controls preload="none" src="/samples/music/SuperMonaco.mp3">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/music/SuperMonaco.mp3</pre>
        <figure>
          <figcaption>Supremacy</figcaption>
            <audio controls preload="none" src="/samples/music/Supremacy.mp3">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/music/Supremacy.mp3</pre>
        <figure>
          <figcaption>Turbo Out Run 1</figcaption>
            <audio controls preload="none" src="/samples/music/Turbo_Outrun_01.mp3">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/music/Turbo_Outrun_01.mp3</pre>
        <figure>
          <figcaption>Turbo Out Run 2</figcaption>
            <audio controls preload="none" src="/samples/music/Turbo_Outrun_02.mp3">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/music/Turbo_Outrun_02.mp3</pre>
        <figure>
          <figcaption>Turbo Out Run 3</figcaption>
            <audio controls preload="none" src="/samples/music/Turbo_Outrun_03.mp3">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/music/Turbo_Outrun_03.mp3</pre>
        <figure>
          <figcaption>Turbo Out Run Ending</figcaption>
            <audio controls preload="none" src="/samples/music/Turbo_Outrun_ending.mp3">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/music/Turbo_Outrun_ending.mp3</pre>
        <figure>
          <figcaption>Turbo Out Run Finish</figcaption>
            <audio controls preload="none" src="/samples/music/Turbo_Outrun_finish.mp3">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/music/Turbo_Outrun_finish.mp3</pre>
        <figure>
          <figcaption>Turbo Out Run Intro</figcaption>
            <audio controls preload="none" src="/samples/music/Turbo_Outrun_intro.mp3">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/music/Turbo_Outrun_intro.mp3</pre>
        <figure>
          <figcaption>Turbo Out Run US Gold</figcaption>
            <audio controls preload="none" src="/samples/music/Turbo_Outrun_us_gold.mp3">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/music/Turbo_Outrun_us_gold.mp3</pre>
        <figure>
          <figcaption>We are Haileybury 8 Bit Version</figcaption>
            <audio controls preload="none" src="/samples/music/we-are-haileybury-8-bit.mp3">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/music/we-are-haileybury-8-bit.mp3</pre>
        <figure>
          <figcaption>Success</figcaption>
            <audio controls preload="none" src="/samples/music/success.mp3">
              Your browser does not support the <code>audio</code> element.
            </audio>
        </figure>
        <pre>/samples/music/success.mp3</pre>
      </div><!-- col-md-9 -->
    </div><!-- row -->
    <?php
      include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
    ?>
  </div><!-- container -->
</body>
</html>

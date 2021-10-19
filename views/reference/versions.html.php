<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <?php
        include __DIR__ . DIRECTORY_SEPARATOR . 'versions-menu.html.php';
      ?>
      <div class="col-md-9">
        <h1>PyAngelo Versions</h1>
        <h2 id="version1.1">Version 1.1</h2>
        <h3 id="major1.1">1.1 Major Changes</h3>
        <div class="well">
          <h4><a href="/blog/the-pyangelo-debugger">PyAngelo debugger added.</a></h4>
          <p>The debugger allows you to</p>
          <ul>
            <li>step into - executes the current line of code written in PyAngelo. It will switch to another file if needed and will dive into functions.</li>
            <li>step over - this will treat functions as a single line of code and move over them in a single step.</li>
            <li>slow motion - this will execute a line of code every 1 second</li>
            <li>continue - the program will run until the next breakpoint</li>
            <li>breakpoints can be created and removed by clicking in the gutter of the editor</li>
            <li>The scope, value, and type of each variable will be displayed ina table</li>
          </ul>
        </div>
        <div class="well">
          <h4>Editor Updated</h4>
          <ul>
            <li>split windows - the code and console output are now displayed in a split window. You can toggle whether this should be displayed vertically or horizontally.</li>
            <li>syntax error detection - the editor now checks for syntax errors in real time and displays a cross in the gutter of the editor if an error is detected. By holding your mouse over the cross you can see the error message.</li>
            <li>improved error messages - if your program contains a run-time error, this will be displayed in the console and the offending line of code will be displayed in the editor.</li>
            <li>image and sound preview - you can now preview images and sounds you have uploaded by clicking on the relevant tab.</li>
          </ul>
        </div>
        <div class="well">
          <h4>Create Classes to Track Students Progress</h4>
          <ul>
            <li>A class can now be <a href="/classes/teacher">created</a></li>
            <li>A unique link will be generated which students can use to join the class</li>
            <li>The owner of the class will be able to view all of their students' sketches</li>
            <li>Students can <a href="/classes/student">see which classes</a> they belong to</li>
          </ul>
        </div>
        <div class="well">
          <h4>Create Collections for your Sketches</h4>
          <ul>
            <li>A collection can be created as a way of organising your sketches.</a></li>
            <li>Each sketch can belong to a single collection.</li>
            <li>You can view all the sketches belonging to a collection.</li>
            <li>You can also still view all sketches.</li>
            <li>If you are looking at a collection and create a new sketch from that page, the new sketch will automatically be put into that collection.</li>
          </ul>
        </div>
        <h3 id="minor1.1">1.1 Minor Changes</h3>
        <ul>
          <li>Dracula colour theme used for the editor</li>
          <li>More colours added to the <a href="/reference#setTextColour">setTextColour() function</a></li>
        </ul>
        <hr />
        <h2 id="version1.0">Version 1.0</h2>
        <p>PyAngelo was updated to use <a href="https://skulpt.org/">Skulpt</a> which is an entirely in-browser implementation of Python.</p>
        <div class="well">
          <h3 id="breaking1.0">1.0 Breaking Changes</h3>
          <h4>@loop_animation removed</h4>
          <p>This command was used to create a game loop. Any code underneath the @loop_animation would have been repeated forever. Any programs using this command need to be updated by changing the @loop_animation to a forever loop. Here is an example:</p>
          <pre>
setCanvasSize(640, 360)
fill(255, 255, 0)
@loop_animation
background(0, 0, 0)
circle(mouseX, mouseY, 15)
          </pre>
          <p>should be changed to:</p>
          <pre>
setCanvasSize(640, 360)
fill(255, 255, 0)
forever:
    background(0, 0, 0)
    circle(mouseX, mouseY, 15)
          </pre>
          <h4>Sprites have been moved to their own library</h4>
          <p>If you were using Sprite, TextSprite, RectangleSprite, CircleSprite, or EllipseSprite, these are now in their own library and hence to make your program work again you need to include the following import statement at the top of your program.</p>
          <pre>
from sprite import *
          </pre>
        </div>
        <h3 id="major1.0">1.0 Major Changes</h3>
        <div class="well">
          <h4>goto and label</h4>
          <ul>
            <li>The "label" keyword is used to create a label</li>
            <li>the "goto" keyword is used to specify which label to jump to</li>
          </ul>
        </div>
        <div class="well">
          <h4>forever</h4>
          <ul>
            <li>forever keyword added to PyAngelo</li>
            <li>This is equivalent to using "while True:"</li>
          </ul>
        </div>
        <h3 id="minor1.0">1.0 Minor Changes</h3>
        <ul>
          <li><a href="/playground">Playground</a> added where user's can code without an account</li>
          <li>Sketches can be soft deleted. They will appear as deleted sketches below all other sketches.</a></li>
          <li>Files within a sketch can be deleted</li>
        </ul>
        <h2 id="version0.1">Version 0.1</h2>
        <p>The first ever version of PyAngelo. This version of PyAngelo used <a href="https://brython.info/">Brython</a> to execute the Python code.</p>
      </div><!-- col-md-9 -->
    </div><!-- row -->
    <?php
      include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
    ?>
  </div><!-- container -->
</body>
</html>

<?php
// colour.html.php
// Documentation for Colour class and related drawing functions: background(), fill(), noFill(), stroke(), noStroke().
?>

<!-- Colour Reference -->
<section id="Colour">
  <h2>Colour</h2>

  <h4>Constructor</h4>
  <pre>
Colour(value: int[, alpha: float])         // Greyscale 0–255, optional alpha 0.0–1.0
Colour(r: int, g: int, b: int[, a: float]) // RGB channels 0–255, optional alpha 0.0–1.0
Colour(css: str)                            // CSS hex, rgb()/rgba(), or named colour
Colour(arr: list[int] | tuple[int])        // Python list or tuple of length 3 or 4
  </pre>

  <h4>Description</h4>
  <p>
    The <code>Colour</code> class represents an RGBA colour. You can create one by:
    <ul>
      <li><strong>Greyscale:</strong> a single integer 0–255, optional alpha.</li>
      <li><strong>RGB(A):</strong> three integers (red, green, blue) 0–255, optional alpha 0.0–1.0.</li>
      <li><strong>CSS string:</strong> hex codes (<code>#RGB</code>, <code>#RRGGBB</code>), <code>rgb()</code>, <code>rgba()</code>, or any <a href="#namedColours"><strong>named colour</strong></a>.</li>
      <li><strong>Array/Tuple:</strong> Python <code>list</code> or <code>tuple</code> of length 3 ([r, g, b]) or 4 ([r, g, b, a]).</li>
    </ul>
  </p>

  <h4 id="namedColours">Named Colours</h4>
  <p>
    <code>c.namedColours</code>: <em>list&lt;str&gt;</em> – a read-only property on <strong>instances</strong> that returns all supported CSS colour names.
    To inspect:
    <pre>c = Colour('red')
print(c.namedColours)  # ['aliceblue', 'antiquewhite', 'aqua', ...]</pre>
    Use these names in <code>background()</code>, <code>fill()</code>, <code>stroke()</code>, etc.
  </p>

  <h4>Instance Properties</h4>
  <ul>
    <li><code>.red</code> — Red channel (0–255)</li>
    <li><code>.green</code> — Green channel (0–255)</li>
    <li><code>.blue</code> — Blue channel (0–255)</li>
    <li><code>.alpha</code> — Alpha (0.0–1.0)</li>
  </ul>

  <h4>Methods</h4>
  <ul>
    <li><code>.css(): str</code> — Returns a CSS-style string <code>"rgba(r, g, b, a)"</code>.</li>
  </ul>

  <h4>Examples</h4>
  <pre>
# Numeric RGB
c1 = Colour(255, 100, 100)

# CSS string
c2 = Colour('#f80')
c3 = Colour('rgba(10,20,30,0.5)')

# Named colour
c4 = Colour('hotpink')

# Greyscale + alpha
c5 = Colour(128, 0.25)

# From Python list
c6 = Colour([10,20,30,1.0])

print(c4.css())            # "rgba(255, 105, 180, 1.00)"
print(len(c1.namedColours), "named colours available")
  </pre>
</section>

<!-- background() Reference -->
<section id="background">
  <h3>background()</h3>

  <h4>Syntax</h4>
  <pre>background(r, g, b[, a]) | background(name: str) | background(col: Colour)</pre>

  <h4>Description</h4>
  <p>
    Sets the entire canvas background colour. Accepts three forms:
    <ul>
      <li><code>r, g, b[, a]</code>: numeric channels (0–255), optional alpha (0.0–1.0).</li>
      <li><code>name</code>: a CSS-named colour string (e.g. <code>'lavender'</code>), from a <a href="#Colour"><code>Colour</code></a> instance’s <a href="#namedColours"><code>namedColours</code></a> list.</li>
      <li><code>col</code>: an existing <a href="#Colour"><code>Colour</code></a> instance.</li>
    </ul>
  </p>

  <h4>Examples</h4>
  <pre>
setCanvasSize(400, 300)

# Grey background
background(200)

# Named-colour background
background('lavender')

# Using a Colour instance
c = Colour('midnightblue', 0.2)
background(c)

# Draw on top
fill('black')
text('Hello!', 20, 30)
  </pre>
</section>

<!-- fill() Reference -->
<section id="fill">
  <h3>fill()</h3>

  <h4>Syntax</h4>
  <pre>fill(r, g, b[, a]) | fill(name: str) | fill(col: Colour)</pre>

  <h4>Description</h4>
  <p>
    Sets the <strong>fill</strong> colour for subsequent shapes. Accepts three forms:
    <ul>
      <li><code>r, g, b[, a]</code>: numeric channels (0–255), optional alpha (0.0–1.0).</li>
      <li><code>name</code>: a CSS-named colour string (e.g. <code>'red'</code>), from an <a href="#Colour"><code>Colour</code></a> instance’s <a href="#namedColours"><code>namedColours</code></a> list.</li>
      <li><code>col</code>: an existing <code>Colour</code> instance.</li>
    </ul>
  </p>

  <h4>Examples</h4>
  <pre>
setCanvasSize(640, 360)

# RGB fill
fill(100, 220, 100)
rect(50, 50, 100, 100)

# Named-colour fill
fill('hotpink')
ellipse(200, 200, 80, 80)

# Colour-instance fill
c = Colour('navy', 0.5)
fill(c)
rect(300, 50, 80, 120)
  </pre>
</section>

<!-- noFill() Reference -->
<section id="noFill">
  <h3>noFill()</h3>

  <h4>Syntax</h4>
  <pre>noFill()</pre>

  <h4>Description</h4>
  <p>
    Disables filling of shapes. After calling <code>noFill()</code>, shapes will be drawn with transparent interiors.
    Use <code>stroke()</code> to set the outline colour.
  </p>

  <h4>Examples</h4>
  <pre>
setCanvasSize(300, 200)

stroke('blue')
noFill()
rect(50, 50, 200, 100)
  </pre>
</section>

<!-- stroke() Reference -->
<section id="stroke">
  <h3>stroke()</h3>

  <h4>Syntax</h4>
  <pre>stroke(r, g, b[, a]) | stroke(name: str) | stroke(col: Colour)</pre>

  <h4>Description</h4>
  <p>
    Sets the outline (stroke) colour for shapes and lines. Accepts three forms:
    <ul>
      <li><code>r, g, b[, a]</code>: numeric channels (0–255), optional alpha (0.0–1.0).</li>
      <li><code>name</code>: a CSS-named colour string.</li>
      <li><code>col</code>: an existing <code>Colour</code> instance.</li>
    </ul>
  </p>

  <h4>Examples</h4>
  <pre>
setCanvasSize(300, 200)

# Black outline by default
stroke(0)
noFill()
ellipse(150, 100, 100, 100)

# Semi-transparent red outline
stroke('red', 0.5)
rect(50, 50, 200, 100)
  </pre>
</section>

<!-- noStroke() Reference -->
<section id="noStroke">
  <h3>noStroke()</h3>

  <h4>Syntax</h4>
  <pre>noStroke()</pre>

  <h4>Description</h4>
  <p>
    Disables drawing outlines for shapes. After calling <code>noStroke()</code>, shapes will only fill interiors.
    Use <code>fill()</code> to set the fill colour.
  </p>

  <h4>Examples</h4>
  <pre>
setCanvasSize(300, 200)

fill('green')
noStroke()
rect(50, 50, 200, 100)
  </pre>
</section>

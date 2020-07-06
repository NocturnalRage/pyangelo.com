<?php
$this->header('HTTP/1.1 404 Not Found');
include __DIR__ . DIRECTORY_SEPARATOR . 'layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . 'layout/navbar.html.php';
?>
  <div class="container">
    <h1>Sorry, the page you are looking for isn't here</h1>
    <p>We're not sure what happened but there are 3 likely causes.</p>
    <h3>Did you get here from a link on our site?</h3>
    <p>Whoops, that is our fault. Please <a href="/contact">send us a message</a> so we can fix the problem and point you to the actual page you were after.</p>
    <h3>Did you get here from another site?</h3>
    <p>Sometimes other sites might have a typo in their link or we've since moved that page. Please <a href="/contact">send us a message</a> explaining how you got here and we'll find the page you were after for you.</p>
    <h3>Did you type the URL yourself?</h3>
    <p>Maybe you typed in the wrong address? Double check what you entered and make sure it looks correct.</p>

<?php
include __DIR__ . DIRECTORY_SEPARATOR . 'layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>

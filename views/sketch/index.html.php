<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <div class="col-md-12 text-center">
        <h1>My Sketches</h1>
          <a href="/sketch/create" class="btn btn-lg btn-primary text-center"
            onclick="event.preventDefault();
            document.getElementById('create-sketch-form').submit();">
              Create a New Sketch
          </a>

          <form id="create-sketch-form" action="/sketch/create" method="POST" style="display: none;">
            <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken']; ?>" />
          </form>
      </div><!-- col-md-12 -->
    </div><!-- row -->
    <div class="row">
      <div class="col-md-12">
         <?php
           include 'sketches.html.php';
         ?>
      <div><!-- twelve columns -->
    <div><!-- row -->
  </div><!-- container -->
<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
?>
</body>
</html>

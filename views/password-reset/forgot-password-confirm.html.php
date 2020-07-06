<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
  
    <h1 class="text-center">Password Reset Instruction Emailed</h1>
    <div class="row">
      <div class="col-md-12">
        <p class="text-center">
        If a matching account was found then an email was sent to <?= $this->esc($email); ?> with instructions on how to reset your password.
        </p>
      </div><!-- col-md-12 -->
    </div><!-- row -->
<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>

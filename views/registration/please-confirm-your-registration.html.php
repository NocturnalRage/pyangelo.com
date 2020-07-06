<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
  
    <h1>You're Almost Done</h1>
    <?php
      include __DIR__ . DIRECTORY_SEPARATOR . '../layout/flash.html.php';
    ?>
    <div class="row">
      <div class="col-md-3">
        <img class="img-responsive img-thumbnail" src="/images/PyAngelo.png" alt="Activate Your Membership">
      </div>
      <div class="col-md-9">
        <h4>Activate Your Free Membership.</h4>
        <p>
          I've just sent you an email to <?= $registeredEmail ?> that contains 
          a confirmation link. In order to activate your free membership, 
          check your email and click on the link in that email. If you don't 
          receive this email then check in your junk folder. If you still 
          can't find the confirmation email then try 
          <a href="/register">registering again</a> and double check your 
          email address.
        </p>
        <p>
          Once you confirm your email address you'll get access to all the 
          tutorials, as well as our free email newsletter.
        </p>
      </div>
    </div><!-- row -->
<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>

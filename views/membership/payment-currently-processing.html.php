<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1 class="text-center">Your Payment is Still Being Processed</h1>
        <p>
        Your payment is currently being processed by the authorities. We'll
        update you when your payment has been received.
        </p>
        <p>
        Thank you for signing up to our monthly subscription and supporting
        the PyAngelo website. When your payment is processed you will have 
        access to every video we have ever made. These videos form a 
        comprehensive guide to help fast-track your coding improvement.
        </p>
        <p>
        Your support is greatly appreciated and assists the development
        and growth of the PyAngelo website.
        </p>
        <h3 class="text-center">Whilst you wait for your payment to be processed</h3>
      </div>
    </div><!-- row -->
    <div class="row">
      <div class="col-md-6">
        <a href="/tutorials">
          <img src="/images/PyAngelo-640x360.png" class="img-responsive featuredThumbnail" alt="Access All Tutorials">
        </a>
        <div class="caption">
          <h3><a href="/tutorials">All Tutorials</a></h3>
          <p>Browse through our free tutorials. There is something for everyone whether you are a beginner or an expert. Each tutorial is aimed to teach you an important coding concept. What are you waiting for? Get started!</p>
          <p>
            <a href="/tutorials" class="btn btn-lg btn-primary" role="button">
               <strong>All Tutorials!</strong>
            </a> 
         </p>
       </div>
      </div><!-- col-md-6 -->

      <div class="col-md-6">
        <a href="/ask-the-teacher">
          <img src="/images/PyAngelo-640x360.png" class="img-responsive featuredThumbnail" alt="Ask the Teacher">
        </a>
        <div class="caption">
          <h3><a href="/ask-the-teacher">Ask the Teacher</a></h3>
          <p>If you ever have a question about coding, our tutorials, or computer science in general, then ask us a question. We will answer your questions with a high priority and may even make a tutorial based on your question.</p>
          <p>
            <a href="/ask-the-teacher" class="btn btn-lg btn-primary" role="button">
               <strong>Ask the Teacher</strong>
            </a> 
         </p>
       </div>
      </div><!-- col-md-6 -->
    </div><!-- row -->
<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>

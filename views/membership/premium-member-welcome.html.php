<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1 class="text-center">Welcome to the PyAngelo Premium Membership</h1>
        <p>
        Thank you for joining as a PyAngelo premium member! As a premium
        member you have online access to every video ever made, and every
        video we will ever make. These videos form a comprehensive guide to
        help fast-track your coding improvement.
        </p>
        <p>
        As a premium member, you now have priority access to comment on videos,
        to engage in discussion, and ask questions about the tutorials.
        Your questions and feedback will be addressed with high priority,
        as well as requests for future content!
        </p>
        <p>
        Your support is greatly appreciated and assists the development
        and growth of the PyAngelo website.
        </p>
      </div>
    </div><!-- row -->
    <div class="row">
      <div class="col-md-6">
        <a href="/tutorials">
          <img src="/images/PyAngelo-640x360.png" class="img-responsive featuredThumbnail" alt="Access All Tutorials">
        </a>
        <div class="caption">
          <h3><a href="/tutorials">All Tutorials</a></h3>
          <p>You now have access to all of our tutorials. There is something for everyone whether you are a beginner or an expert. Each tutorial is aimed to teach you an important coding concept. What are you waiting for? Get started!</p>
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

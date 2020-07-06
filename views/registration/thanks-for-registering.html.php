<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1 class="text-center">Thanks for Joining PyAngelo</h1>
        <p>
        You've just joined an awesome community learning the art of
        programming! Whether you don't yet know how to code,
        or you're an experienced code, PyAngelo has something
        for you. You should receive a welcome email explaining how to get
        the most out of this site. We usually send out email
        newsletters every week or two. To make sure you receive it,
        please add the email address
        <a href="mailto:jeff@nocturnalrage.com">jeff@nocturnalrage.com</a>
        to your address book.
        </p>
        <p>
        We REALLY hate spam, so your email address is super safe with us.
        Every newsletter we send includes an unsubscribe link. One click,
        and you're out!
        </p>
      </div>
    </div><!-- row -->
    <div class="row">
      <div class="col-md-6">
        <a href="/tutorials/introduction-to-pyangelo">
          <img src="/images/PyAngelo.png" class="img-responsive featuredThumbnail" alt="Introduction to PyAngelo">
        </a>
        <div class="caption">
          <h3><a href="/tutorials/introduction-to-pyangelo">Introduction to PyAngelo</a></h3>
          <p>This is a great place to start. We teach you step by step how to start programming with Python in the Browswer. We call it PyAngelo!</p>
          <p>
            <a href="/tutorials/introduction-to-pyangelo" class="btn btn-lg btn-primary" role="button">
              <strong>Start Coding!</strong>
            </a> 
         </p>
       </div>
      </div><!-- col-md-6 -->

      <div class="col-md-6">
        <a href="/blog">
          <img src="/images/PyAngelo.png" class="img-responsive featuredThumbnail" alt="Read the PyAngelo Blog">
        </a>
        <div class="caption">
          <h3><a href="/blog">Read the Blog</a></h3>
          <p>Every couple of weeks we write a blog post. Different topics include programming advice, PyAngelo news, and the wider world of coding!</p>
            <a href="/blog" class="btn btn-lg btn-primary" role="button">
              <strong>Check it out!</strong>
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

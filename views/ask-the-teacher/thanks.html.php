<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <img class="img-responsive featuredThumbnail pull-right" src="/images/jeff-plumb.jpg" alt="Thanks for your question">
    <h1>Thanks For Submitting Your Question</h1>
    <p>We love to talk about coding so it is great to receive your question. As we get a lot of questions it may take some time before we get to answer this one. We will try and get to it within the next 7 days. Your question will appear on the website as soon as we have provided an answer.</p>
    <p>Here's the question you asked us!</p>
    <hr />
    <h2><?= $this->esc($question['question_title']) ?></h2>
    <div><?= $purifier->purify($question['question']); ?></div>
    <?php
      include __DIR__ . '/../layout/footer.html.php';
    ?>
  </div><!-- container -->
</body>
</html>

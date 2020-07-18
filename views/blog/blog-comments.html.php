    <hr />
    <?php $totalComments = count($comments); ?>
    <div class="row">
      <div class="col-md-12 add-bottom">
        <?php if ($totalComments > 0) : ?>
          <h3 id="comment-anchor">Thoughts on this blog</h3>
        <?php else : ?>
          <h3 id="comment-anchor">No comments yet!</h3>
        <?php endif; ?>
        <a id="alertStatus"
           class="btn <?= $alertUser ? 'btn-info' : 'btn-primary' ?>"
           href="#"
           data-blog-id="<?= $this->esc($blog['blog_id']); ?>"
           data-crsf-token="<?= $personInfo['crsfToken']; ?>"
           aria-label="Toggle Alert">
          <i class="fa fa-bookmark" aria-hidden="true"></i>
          <?= $alertUser ? 'Stop notifications' : 'Notify me of updates' ?>
        </a>
      </div><!-- comments md-col-12 -->
    </div><!-- row -->
    <?php if ($totalComments > 0) : ?>
      <?php $commentCount = 0; ?>
      <div class="row">
        <div id="comments" class="col-md-12">
          <?php if ($totalComments - $showCommentCount > 0) : ?>
            <div id="showMoreCommentsDiv">
              <a id="showMoreComments" href="#"> Show <?= $this->esc($totalComments - $showCommentCount); ?> more comment<?= ($totalComments - $showCommentCount) == 1 ? '' : 's' ?></a>
            </div>
          <?php endif; ?>
          <?php foreach($comments as $comment) : ?>
            <?php
              $commentCount++;
              if ($commentCount > ($totalComments - $showCommentCount)) {
                $mediaDisplayClass = 'displayComment';
              }
              else {
                $mediaDisplayClass = 'hideComment';
              }
              include __DIR__ . '/blog-comment.html.php';
            ?>
          <?php endforeach; ?>
        </div><!-- comments md-col-12 -->
      </div><!-- row -->
    <?php endif; ?>
    <?php if ($personInfo['loggedIn']) : ?>
      <div class="row">
        <div id="add-comment" class="md-col-12">
          <form id="commentForm" method="post" action="#">
            <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken']; ?>" />
            <input type="hidden" name="blogId" value="<?= $this->esc($blog['blog_id']); ?>" />
            <div class="form-group">
              <label for="blogComment" class="control-label">Add your thoughts:</label>
              <textarea name="blogComment" id="blogComment" class="form-control tinymce" placeholder="Join the discussion..." rows="8"></textarea>
            </div>
            <div id="submitButtonDiv" class="form-group">
            </div>
          </form>
        </div><!-- add-comment md-col-12 -->
      </div><!-- row -->
    <?php else : ?>
      <hr />
      <p>Become a <a href="/register">free member</a> to post a comment about this blog.</p>
    <?php endif; ?>

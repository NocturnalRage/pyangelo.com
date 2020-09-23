    <?php $totalComments = count($comments); ?>
    <div class="row">
      <div class="col-md-12 add-bottom">
        <?php if ($totalComments > 0) : ?>
          <h3 id="comment-anchor">Thoughts on this question</h3>
        <?php else : ?>
          <h3 id="comment-anchor">No comments yet!</h3>
        <?php endif; ?>
      </div><!-- md-col-12 add-bottom -->
    </div><!-- row -->
    <?php if ($totalComments > 0) : ?>
      <?php $commentCount = 0; ?>
      <div class="row">
        <div id="comments" class="col-md-12">
          <?php if ($totalComments - $showCommentCount > 0) : ?>
            <div id="showMoreCommentsDiv">
              <a id="showMoreComments" href="#"> Show <?= $this->esc($totalComments - $showCommentCount); ?> more comments</a>
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
              if ($comment['admin']) {
                include __DIR__ . '/question-comment-admin.html.php';
              }
              else {
                include __DIR__ . '/question-comment.html.php';
              }
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
            <input type="hidden" name="questionId" value="<?= $this->esc($question['question_id']); ?>" />
            <div class="form-group">
              <label for="questionComment" class="control-label">Add your thoughts:</label>
              <textarea name="questionComment" id="questionComment" class="form-control tinymce" placeholder="Join the discussion..." rows="8"></textarea>
            </div>
            <div id="submitButtonDiv" class="form-group">
            </div>
          </form>
        </div><!-- add-comment md-col-12 -->
      </div><!-- row -->
    <?php else : ?>
      <hr />
      <p>Become a <a href="/register">free member</a> to post a comment about this question.</p>
    <?php endif; ?>

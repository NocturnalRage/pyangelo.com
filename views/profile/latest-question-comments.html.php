    <div class="row">
      <div class="col-md-12">
        <h1 id="question_comments">Latest Comments on Ask the Teacher Questions</h1>
        <div class="table-responsive">
          <table id="question-comments-table" class="table table-striped table-hover">
            <thead>
              <colgroup>
                <col class="col-xs-7"></col>
                <col class="col-xs-3"></col>
                <col class="col-xs-2"></col>
              </colgroup>
              <tr>
                <th>Lesson</th>
                <th>User</th>
                <th>Time</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($questionComments as $questionComment) : ?>
                <?php if ($questionComment['admin']) : ?>
                  <?php $trClass = "info"; ?>
                <?php else : ?>
                  <?php $trClass = "user"; ?>
                <?php endif; ?>
                <tr class="<?= $trClass ?>">
                  <td>
                    <?php
                      $commentLink = '/ask-the-teacher/' . $questionComment['slug'] .  '#comment_' . $questionComment['comment_id'];
                    ?>
                  <a href="<?= $this->esc($commentLink) ?>">
                      <?= $this->esc($questionComment['question_title']); ?>
                    </a>
                  </td>
                  <td>
                    <?= $this->esc($questionComment['display_name']); ?>
                  </td>
                  <?php
                    $commentDate = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $questionComment['created_at'])->diffForHumans();
                  ?>
                  <td><?= $commentDate ?></td>
                </tr>
              <?php endforeach; ?>
            <tbody>
          </table>
        </div>
      </div><!-- col-md-12 -->
    </div><!-- row -->
    <div class="row add-bottom">
      <?php if (count($questionComments) < 1) : ?>
        <span class="pull-left">&larr;
          <a href="/latest-comments">
            View 1st Page
          </a>
        </span>
      <?php elseif ($questionPageNo > 1) : ?>
        <span class="pull-left">&larr;
          <a href="/latest-comments?lessonPageNo=<?= $lessonPageNo ?>&questionPageNo=<?= $questionPageNo-1 ?>&blogPageNo=<?= $blogPageNo ?>#question_comments">
            Page <?= $this->esc($questionPageNo - 1) ?>
          </a>
        </span>
      <?php endif; ?>
      <?php if (count($questionComments) == $commentsPerPage) : ?>
        <span class="pull-right">
          <a href="/latest-comments?lessonPageNo=<?= $lessonPageNo ?>&questionPageNo=<?= $questionPageNo + 1 ?>&blogPageNo=<?= $blogPageNo ?>#question_comments">
            Page <?= $this->esc($questionPageNo+1) ?>&rarr;
          </a>
        </span>
      <?php endif; ?>
    </div>

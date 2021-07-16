    <div class="row">
      <div class="col-md-12">
        <h1 id="lesson_comments">Latest Comments on Video Lessons</h1>
        <div class="table-responsive">
          <table id="lesson-comments-table" class="table table-striped table-hover">
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
              <?php foreach ($lessonComments as $lessonComment) : ?>
                <?php if ($lessonComment['admin']) : ?>
                  <?php $trClass = "info"; ?>
                <?php else : ?>
                  <?php $trClass = "user"; ?>
                <?php endif; ?>
                <tr class="<?= $trClass ?>">
                  <td>
                    <?php
                      $commentLink = '/tutorials/' . $lessonComment['tutorial_slug'] . '/' . $lessonComment['lesson_slug'] .  '#comment_' . $lessonComment['comment_id'];
                    ?>
                  <a href="<?= $this->esc($commentLink) ?>">
                      <?= $this->esc($lessonComment['lesson_title']); ?>
                    </a>
                  </td>
                  <td>
                    <?= $this->esc($lessonComment['display_name']); ?>
                  </td>
                  <?php
                    $commentDate = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $lessonComment['created_at'])->diffForHumans();
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
      <?php if (count($lessonComments) < 1) : ?>
        <span class="pull-left">&larr;
          <a href="/latest-comments">
            View 1st Page
          </a>
        </span>
      <?php elseif ($lessonPageNo > 1) : ?>
        <span class="pull-left">&larr;
          <a href="/latest-comments?lessonPageNo=<?= $lessonPageNo-1 ?>&questionPageNo=<?= $questionPageNo ?>&blogPageNo=<?= $blogPageNo ?>#lesson_comments">
            Page <?= $this->esc($lessonPageNo-1) ?>
          </a>
        </span>
      <?php endif; ?>
      <?php if (count($lessonComments) == $commentsPerPage) : ?>
        <span class="pull-right">
          <a href="/latest-comments?lessonPageNo=<?= ($lessonPageNo+1) ?>&questionPageNo=<?= $questionPageNo ?>&blogPageNo=<?= $blogPageNo ?>#lesson_comments">
            Page <?= $this->esc($lessonPageNo+1) ?>&rarr;
          </a>
        </span>
      <?php endif; ?>
    </div>

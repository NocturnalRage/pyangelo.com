    <div class="row">
      <div class="col-md-12">
        <h1 id="blog_comments">Latest Comments on Blog Posts</h1>
        <div class="table-responsive">
          <table id="blog-comments-table" class="table table-striped table-hover">
            <thead>
              <colgroup>
                <col class="col-xs-7"></col>
                <col class="col-xs-3"></col>
                <col class="col-xs-2"></col>
              </colgroup>
              <tr>
                <th>Blog</th>
                <th>User</th>
                <th>Time</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($blogComments as $blogComment) : ?>
                <?php if ($blogComment['admin']) : ?>
                  <?php $trClass = "info"; ?>
                <?php else : ?>
                  <?php $trClass = "user"; ?>
                <?php endif; ?>
                <tr class="<?= $trClass ?>">
                  <td>
                    <?php
                      $commentLink = '/blog/' . $blogComment['slug'] . '#comment_' . $blogComment['comment_id'];
                    ?>
                  <a href="<?= $this->esc($commentLink) ?>">
                      <?= $this->esc($blogComment['title']); ?>
                    </a>
                  </td>
                  <td>
                    <?= $this->esc($blogComment['display_name']); ?>
                  </td>
                  <?php
                    $commentDate = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $blogComment['created_at'])->diffForHumans();
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
      <?php if (count($blogComments) < 1) : ?>
        <span class="pull-left">&larr;
          <a href="/latest-comments">
            View 1st Page
          </a>
        </span>
      <?php elseif ($blogPageNo > 1) : ?>
        <span class="pull-left">&larr;
          <a href="/latest-comments?lessonPageNo=<?= $lessonPageNo ?>&questionPageNo=<?= $questionPageNo ?>&blogPageNo=<?= $blogPageNo-1 ?>">
            Page <?= $this->esc($blogPageNo-1) ?>
          </a>
        </span>
      <?php endif; ?>
      <?php if (count($blogComments) == $commentsPerPage) : ?>
        <span class="pull-right">
          <a href="/latest-comments?lessonPageNo=<?= $lessonPageNo ?>&questionPageNo=<?= $questionPageNo ?>&blogPageNo=<?= ($blogPageNo+1) ?>">
            Page <?= $this->esc($blogPageNo+1) ?>&rarr;
          </a>
        </span>
      <?php endif; ?>
    </div>

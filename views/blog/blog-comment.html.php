        <div class="media <?= $mediaDisplayClass ?>" id="comment_<?= $comment['comment_id'] ?>">
          <div class="media-left">
            <img class="media-object" src="<?= $avatar->getAvatarUrl($comment['email']) ?>" alt="<?= $this->esc($comment['display_name']) ?>" />
          </div>
          <div class="media-body">
            <h4 class="media-heading"><?= $this->esc($comment['display_name']) ?> <small><i>Posted <?= $this->esc($comment['created_at']) ?></i></small></h4>
            <div><?= $purifier->purify($comment['blog_comment']) ?></div>
            <?php
              if ($personInfo['isAdmin']) {
                include __DIR__ . '/blog-comment-unpublish-form.html.php';
                include __DIR__ . '/show-user.html.php';
              }
            ?>
          </div>
          <hr />
        </div>

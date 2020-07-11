<?php
include __DIR__ . '/../layout/header.html.php';
include __DIR__ . '/../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <?php if ($selection == 'all') : ?>
          <a class="btn btn-info" href="/notifications" aria-label="Show unread notifications">Show unread notifications</a>
        <?php else : ?>
          <a class="btn btn-info" href="/notifications?all=1" aria-label="Show all notifications">Show all notifications</a>
        <?php endif; ?>
      </div><!-- col-md-12 -->
    </div><!-- row -->
    <div class="row">
      <div class="col-md-12">
        <?php if ($notifications) : ?>
          <a id="markAllAsRead"
             class="pull-right btn btn-default"
             href="#"
             aria-label="Mark all notifications as read">
            <i class="fa fa-bookmark" aria-hidden="true"></i> Mark all as read
            
          </a>
          <h1><?= $selection == 'all' ? '' : 'Unread ' ?>Notifications</h1>
          <div class="table-responsive">
          <table id="notifications-table" data-crsf-token="<?= $personInfo['crsfToken'] ?>" data-selection="<?= $selection ?>" class="table table-striped table-hover">
              <?php foreach ($notifications as $notification) : ?>
                <?php $data = json_decode($notification['data']); ?>
                <?php if ($notification['has_been_read']) : ?>
                  <?php if ($data->isAdmin) : ?>
                    <?php $trClass = "read info"; ?>
                  <?php else : ?>
                    <?php $trClass = "read warning"; ?>
                  <?php endif; ?>
                <?php else : ?>
                  <?php if ($data->isAdmin) : ?>
                    <?php $trClass = "unread success"; ?>
                  <?php else : ?>
                    <?php $trClass = "unread"; ?>
                  <?php endif; ?>
                <?php endif; ?>
                <tr id="notification<?= $notification['notification_id']; ?>" class="<?= $trClass ?>">
                  <td>
                    <a class="notification-link"
                       data-notification-id="<?= $notification['notification_id'] ?>"
                       href="<?= $this->esc($data->link) ?>"
                    >
                      <?= $this->esc($data->message); ?>
                    </a>
                  </td>
                  <td>
                    <img src="<?= $this->esc($data->avatarUrl); ?>" alt="I hope you like my comment" />
                  </td>
                  <?php
                    $notificationDate = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notification['created_at'])->diffForHumans();
                  ?>
                  <td><?= $notificationDate ?></td>
                  <td>
                    <a class="thread-unsubscribe"
                       data-notification-type="<?= $notification['notification_type'] ?>"
                       data-notification-type-id="<?= $notification['notification_type_id'] ?>"
                       href="#"
                       title="Unsubscribe from thread"
                    >
                      <i class="fa fa-ban fa-lg"></i>
                    </a>
                  </td>
                  <td>
                    <?php if ($notification['has_been_read']) : ?>
                      &nbsp;
                    <?php else : ?>
                      <a id="markAsRead<?= $notification['notification_id']; ?>"
                         class="markAsRead"
                         data-notification-id="<?= $notification['notification_id'] ?>"
                         href="#"
                         title="Mark as read"
                      >
                        <i class="fa fa-check fa-lg"></i>
                      </a>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </table>
          </div>
        <?php else : ?>
          <h1>No <?= $selection == 'all' ? '' : 'Unread ' ?>Notifications</h1>
          <p>You'll see updates here when someone adds a comment to a blog or lesson that you are following.</p>
        <?php endif; ?>
      </div><!-- col-md-12 -->
    </div><!-- row -->
    <?php
      include __DIR__ . '/../layout/footer.html.php';
    ?>
  </div><!-- container -->
  <script src="<?= mix('js/notify.min.js'); ?>"></script>
  <script src="<?= mix('js/notifications.js'); ?>"></script>
</body>
</html>

    <div class="table-responsive lessons-table">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Question Title</th>
            <th>Name</th>
            <th>Asked</th>
            <th>Delete</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($questions as $question) : ?>
            <tr>
              <td>
                <a href="/ask-the-teacher/<?= $this->esc($question['slug']); ?>/edit">
                  <?= $this->esc($question['question_title']); ?>
                </a>
              </td>
              <td>
                <a href="/admin/users/<?= $this->esc($question['person_id']); ?>">
                  <?= $question['display_name']; ?>
                </a>
              </td>
              <?php
                $askedAt = \Carbon\Carbon::createFromFormat(
                  'Y-m-d H:i:s',
                  $question['created_at']
                )->diffForHumans();
              ?>
              <td><?= $this->esc($askedAt) ?></td>
              <td>
                <form action="/ask-the-teacher/<?= $this->esc($question['slug']); ?>/delete" method="post">
                  <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken']; ?>" />
                  <button type="submit" class="pull-right btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this question?')">
                    <i class="fa fa-times"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div><!-- table-responsive -->

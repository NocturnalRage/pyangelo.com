    <div class="table-responsive lessons-table">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Question Title</th>
            <th>Category</th>
            <th>Last Updated</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($questions as $question) : ?>
            <tr>
              <td>
                <a href="/ask-the-teacher/<?= $this->esc($question['slug']); ?>">
                  <?= $question['question_title']; ?>
                </a>
              </td>
              <td>
                <a href="/ask-the-teacher/topic/<?= $this->esc($question['category_slug']); ?>">
                  <?= $question['question_category']; ?>
                </a>
              </td>
                <?php
                  $lastUpdated = \Carbon\Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    $question['updated_at']
                  )->diffForHumans();
                ?>
                <td><?= $this->esc($lastUpdated) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div><!-- table-responsive -->

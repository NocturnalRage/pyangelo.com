    <div class="table-responsive lessons-table">
      <table class="table table-striped table-hover">
        <thead
          <tr>
            <th>Question Title</th>
            <th>Asked</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($unansweredQuestions as $question) : ?>
            <tr>
              <td>
                <?= $question['question_title']; ?>
              </td>
                <?php
                  $createdAt = \Carbon\Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    $question['created_at']
                  )->diffForHumans();
                ?>
                <td><?= $this->esc($createdAt) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div><!-- table-responsive -->

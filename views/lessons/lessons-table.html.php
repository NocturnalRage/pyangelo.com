    <div class="table-responsive lessons-table">
      <table class="table table-striped table-hover">
        <tbody>
          <?php $lessonNo = 1; ?>
          <?php foreach($lessons as $lessonItem) : ?>
            <tr<?= $lessonItem['lesson_id'] == ($lesson['lesson_id'] ?? 0) ? ' class="success"' : '' ?>>
              <td><?= $lessonNo++; ?></td>
              <td>
                <a class="toggleComplete btn <?= $lessonItem['completed'] ? 'btn-success' : 'btn-default' ?> btn<?= $this->esc($lessonItem['lesson_id']); ?>" href="#" data-lesson-id="<?= $this->esc($lessonItem['lesson_id']); ?>" aria-label="Toggle completion">
                  <i class="fa fa-check" aria-hidden="true"></i>
                </a>
              </td>
              <td>
                <?php if ($lessonItem["lesson_security_level_id"] == 3) : ?>
                  <i class="fa fa-lock" aria-hidden="true" title="This video is for premium members only"></i>
                <?php endif; ?>
              </td>
              <td>
                <a href="/tutorials/<?= $this->esc($lessonItem['tutorial_slug']); ?>/<?= $this->esc($lessonItem['lesson_slug']); ?>">
                  <?= $lessonItem['lesson_title']; ?>
                </a>
              </td>
              <td class="text-right"><?= $lessonItem['display_duration']; ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div><!-- table-responsive -->


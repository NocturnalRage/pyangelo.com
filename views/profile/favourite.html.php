            <h3>
              <a href="/tutorials/<?= $this->esc($favourite['tutorial_slug']) ?>/<?= $this->esc($favourite['lesson_slug']) ?>">
              <i class="fa <?= $favourite['completed'] ? 'fa-check-square-o' : 'fa-minus-square-o' ?>" aria-hidden="true"></i>
                <?= $this->esc($favourite['lesson_title']) ?>
              </a>
            </h3>
            <p><small><?= $this->esc($favourite['display_duration']) ?></small></p>
            <p><?= $this->esc($favourite['tutorial_title']) ?></p>
            <hr />

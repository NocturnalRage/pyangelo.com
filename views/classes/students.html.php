    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <tbody>
          <?php foreach($students as $student) : ?>
          <?php
            $joinedAt = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $student['joined_at'])->diffForHumans();
          ?>

            <tr>
              <td>
                  <h3><a href="/classes/teacher/<?= $this->esc($class['class_id']) ?>/<?= $this->esc($student['person_id']) ?>"><?= $this->esc($student["given_name"]) ?> <?= $this->esc($student["family_name"]) ?></a></h3>
              </td>
              <td>
                 <p class="sketchCell">Joined: <em><?= $this->esc($joinedAt) ?></em></p>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div><!-- table-responsive -->

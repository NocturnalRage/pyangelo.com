    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <tbody>
          <?php foreach($classes as $class) : ?>
          <?php
            $joinedAt = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $class['joined_at'])->diffForHumans();
          ?>

            <tr>
              <td>
                  <h3><?= $this->esc($class['class_name']) ?></h3>
              </td>
              <td>
                 <p class="sketchCell">Joined: <em><?= $this->esc($joinedAt) ?></em></p>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div><!-- table-responsive -->

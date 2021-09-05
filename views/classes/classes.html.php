    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <tbody>
          <?php foreach($classes as $class) : ?>
          <?php
            $createdDate = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $class['created_at'])->diffForHumans();
          ?>

            <tr>
              <td>
                  <h3><a href="/classes/teacher/<?= $this->esc($class['class_id']) ?>"><?= $this->esc($class['class_name']) ?></a></h3>
              </td>
              <td>
                 <p class="sketchCell">Created: <em><?= $this->esc($createdDate) ?></em></p>
              </td>
              <td class="text-right">
                  <form action="/classes/teacher/<?= $this->esc($class['class_id']); ?>/archive" method="post">
                    <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken']; ?>" />
                    <button type="submit" class="pull-right btn btn-sm btn-warning sketchCell" onclick="return confirm('Are you sure you want to archive this class?')">
                      <i class="fa fa-archive"></i>
                    </button>
                  </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div><!-- table-responsive -->

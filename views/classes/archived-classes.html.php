    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <tbody>
          <?php foreach($archivedClasses as $class) : ?>
          <?php
            $archivedDate = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $class['archived_at'])->diffForHumans();
          ?>
            <tr>
              <td>
                  <h3><?= $this->esc($class['class_name']) ?></h3>
              </td>
              <td>
                 <p class="sketchCell">Archived: <em><?= $this->esc($archivedDate) ?></em></p>
              </td>
              <td class="text-right">
                  <form action="/classes/teacher/<?= $this->esc($class['class_id']); ?>/restore" method="post">
                    <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken']; ?>" />
                    <button type="submit" class="pull-right btn btn-sm btn-success sketchCell" onclick="return confirm('Are you sure you want to restore this class?')">
                      <i class="fa fa-check"></i>
                    </button>
                  </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div><!-- table-responsive -->

    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <tbody>
          <?php foreach($sketches as $sketch) : ?>
          <?php
            $updatedAt = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sketch['updated_at'])->diffForHumans();
          ?>

            <tr>
              <td>
                  <h3><a href="/sketch/<?= $this->esc($sketch['sketch_id']) ?>"><?= $this->esc($sketch['title']) ?></a></h3>
              </td>
              <td>
                 <p class="sketchCell">Updated: <em><?= $this->esc($updatedAt) ?></em></p>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div><!-- table-responsive -->

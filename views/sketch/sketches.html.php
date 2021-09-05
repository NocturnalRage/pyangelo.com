<div class="row">
  <div class="col-md-12">
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <tbody>
          <?php foreach($sketches as $sketch) : ?>
          <?php
            $lastUpdatedDate = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sketch['updated_at'])->diffForHumans();
          ?>

            <tr>
              <td>
                  <h3><a href="/sketch/<?= $this->esc($sketch['sketch_id']) ?>"><?= $this->esc($sketch['title']) ?></a></h3>
              </td>
              <td>
                 <p class="sketchCell">Last saved: <em><?= $this->esc($lastUpdatedDate) ?></em></p>
              </td>
              <td class="text-right">
                  <form action="/sketch/<?= $this->esc($sketch['sketch_id']); ?>/delete" method="post">
                    <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken']; ?>" />
                    <button type="submit" class="pull-right btn btn-sm btn-danger sketchCell" onclick="return confirm('Are you sure you want to delete this sketch?')">
                      <i class="fa fa-times"></i>
                    </button>
                  </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div><!-- table-responsive -->
  <div><!-- twelve columns -->
<div><!-- row -->

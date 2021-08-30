<div class="row">
  <div class="col-md-12">
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <tbody>
          <?php foreach($deletedSketches as $sketch) : ?>
          <?php
            $deletedDate = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sketch['deleted_at'])->diffForHumans();
          ?>
            <tr>
              <td>
                  <h3><?= $this->esc($sketch['title']) ?></h3>
              </td>
              <td>
                 <p class="sketchCell">Deleted: <em><?= $this->esc($deletedDate) ?></em></p>
              </td>
              <td class="text-right">
                  <form action="/sketch/<?= $this->esc($sketch['sketch_id']); ?>/restore" method="post">
                    <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken']; ?>" />
                    <button type="submit" class="pull-right btn btn-sm btn-success sketchCell" onclick="return confirm('Are you sure you want to restore this sketch?')">
                      <i class="fa fa-check"></i>
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

<div class="row">
  <div class="col-md-12">
  <div><!-- twelve columns -->
<div><!-- row -->

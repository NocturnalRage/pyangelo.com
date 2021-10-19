  <div class="col-md-9">
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
                 <select name="collection" class="collectionSelect form-control" data-sketch-id="<?= $this->esc($sketch['sketch_id']); ?>" data-crsf-token="<?= $personInfo['crsfToken']; ?>">
                 <option value="0">No collection</option>
                 <?php foreach($collections as $collection) : ?>
                    <option <?php if ($sketch['collection_id'] == $collection['collection_id']) echo ' selected '; ?> value="<?= $this->esc($collection['collection_id']); ?>"><?= $this->esc($collection['collection_name']); ?></option>
                 <?php endforeach; ?>
                 </select>
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
  </div><!-- nine columns -->

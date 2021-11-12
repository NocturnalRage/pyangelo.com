<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <div class="col-md-12 text-center add-bottom">
        <h1><?= $this->esc($collection['collection_name']) ?></h1>
      </div><!-- col-md-12 -->
    </div><!-- row -->
    <div class="row">
      <div class="col-md-12">
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>Sketch Name</th>
                <th>Last Saved</th>
                <th>Created</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($sketches as $sketch) : ?>
              <?php
                $lastUpdatedDate = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sketch['updated_at'])->diffForHumans();
                $createdDate = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sketch['created_at'])->diffForHumans();
              ?>

                <tr>
                  <td>
                      <h3><a href="/sketch/<?= $this->esc($sketch['sketch_id']) ?>"><?= $this->esc($sketch['title']) ?></a></h3>
                  </td>
                  <td>
                     <p class="sketchCell"><em><?= $this->esc($lastUpdatedDate) ?></em></p>
                  </td>
                  <td>
                     <p class="sketchCell"><em><?= $this->esc($createdDate) ?></em></p>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div><!-- table-responsive -->
      </div><!-- twelve columns -->
    </div><!-- row -->
  </div><!-- container -->
<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
?>
</body>
</html>

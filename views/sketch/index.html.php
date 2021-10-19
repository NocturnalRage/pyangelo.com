<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <?php include __DIR__ . '/../layout/flash.html.php'; ?>
    <div class="row">
      <div class="col-md-12 text-center add-bottom">
        <h1>My Sketches</h1>
          <a href="/sketch/create" class="btn btn-lg btn-primary text-center"
            onclick="event.preventDefault();
            document.getElementById('create-sketch-form').submit();">
              Create a New Sketch
          </a>

          <form id="create-sketch-form" action="/sketch/create" method="POST" style="display: none;">
            <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken']; ?>" />
            <input type="hidden" name="collectionId" value="<?= $activeCollectionId; ?>" />
          </form>
      </div><!-- col-md-12 -->
    </div><!-- row -->
    <div class="row">
      <div class="col-md-3">
        <div class="well">
          <h2>Collections</h2>
          <a id="newCollection" href="/collection/create">
            <i class="fa fa-folder-open" aria-hidden="true"></i> Create Collection
          </a>
          <hr />
          <form id="new-collection-form" class="form-horizontal" action="/collection/create" method="POST" style="display: none;">
            <input type="hidden" name="crsfToken" id="crsfToken" value="<?= $personInfo['crsfToken'] ?>" />
            <input type="text" name="collectionTitle" id="collectionTitle" class="form-control" value="" maxlength="100" required autofocus />
            <button id="newCollectionSubmit" class="btn btn-success newCollectionSubmit"><i class="fa fa-folder" aria-hidden="true"></i> Create Collection</button>
            <button id="newCollectionCancel" class="btn btn-danger newCollectionCancel"><i class="fa fa-window-close" aria-hidden="true"></i> Cancel</button>
          </form>

          <div class="collections">
            <a href="/sketch" class="collection<?php if ($activeCollectionId == 0) echo(' active'); ?>">All Sketches</a>
            <?php foreach($collections as $collection) : ?>
              <a href="/collection/<?= $this->esc($collection['collection_id']) ?>" class="collection<?php if ($activeCollectionId == $collection['collection_id']) echo(' active'); ?>"><?= $this->esc($collection['collection_name']) ?></a>
            <?php endforeach; ?>
          </div>
        </div>
      </div><!-- col-md-3 -->
    <?php
      include 'sketches.html.php';
    ?>
    </div><!-- row -->
    <?php if (! empty($deletedSketches)) : ?>
      <hr />
      <div class="row">
        <div class="col-md-12 text-center add-bottom">
          <h2>Deleted Sketches</h2>
        </div><!-- col-md-12 -->
      </div><!-- row -->
      <?php
        include 'deleted-sketches.html.php';
      ?>
    <?php endif; ?>
  </div><!-- container -->

<script src="<?= mix('js/collection.js'); ?>"></script>

<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
?>
</body>
</html>

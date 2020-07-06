<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row text-center">
    <h1>Sort <?= $this->esc($category['category']) ?></h1>
      <a href="/categories/<?= $this->esc($category['category_slug']) ?>" class="btn btn-warning">
        <i class="fa fa fa-history"></i> Back to Tutorials</a>

    </div>
    <div class="col-md-8 col-md-offset-2 text-center">
      <hr />
    </div>
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <ul id="sortable" class="list-group">
          <?php foreach($tutorials as $tutorial) : ?>
            <li id="<?= $this->esc($tutorial['slug']); ?>" class=" list-group-item"><?= $this->esc($tutorial['title']); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div><!-- row -->
<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
?>
  </div><!-- container -->
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="<?= mix('js/notify.min.js'); ?>"></script>
  <script>
  $(function() {
    $("#sortable").sortable({
      start : function(event, ui) {
        var start_pos = ui.item.index();
        ui.item.data('start_pos', start_pos);
      },
      change : function(event, ui) {
        var start_pos = ui.item.data('start_pos');
        var index = ui.placeholder.index();
        if (start_pos < index) {
          $('#sortable li:nth-child(' + index + ')').addClass('list-group-item-success');
        } else {
          $('#sortable li:eq(' + (index + 1) + ')').addClass('list-group-item-success');
        }
      },
      update : function(event, ui) {
          $('#sortable li').removeClass('list-group-item-success');
      },
      stop: function(event, ui) {
        var idsInOrder = $("#sortable").sortable("toArray");
        $.ajax({
        url: '/categories/<?= $this->esc($category['category_slug']) ?>/save-sort-order',
          type: 'POST',
          data: {idsInOrder:idsInOrder}
        })
        .done(function(data) {
          if (data.status == "success") {
            $.notify(data.message, { className: data.status, position:"right-bottom" });
          }
          else if (data.status == "error") {
            $.notify(data.message, { className: data.status, position:"right-bottom" });
          }
        })
        .fail(function() {
          alert('There was an error and the order of the tutorials could not be updated', { className: 'error', position:"right-bottom" });
        })
        .always(function() {
          // Do something only if requried
        });
      }
    });
    $( "#sortable" ).disableSelection();
  } );
  </script>
</body>
</html>

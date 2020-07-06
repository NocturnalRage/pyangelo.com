    <div class="row">
      <div class="col-md-12">
        <p class="appHeading">
          <button id="startStop" class="btn btn-success">Start</button>
        </p>
        <?php if ($personInfo['details']['person_id'] != $sketch['person_id']) : ?>
          <p class="appHeading">
            <a id="fork"
               class="button"
               href="/sketch/<?= $sketch['sketch_id'] ?>/fork"
               onclick="event.preventDefault();
               document.getElementById('fork-form').submit();">
               Fork this sketch
            </a>
          </p>
          <form id="fork-form" action="/sketch/<?=$sketch['sketch_id'] ?>/fork" method="POST" style="display: none;">
            <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken'] ?>" />
          </form>
        <?php endif; ?>
      </div><!-- col-md-12  -->
    </div><!-- row -->

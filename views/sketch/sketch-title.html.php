    <div class="row">
      <div class="col-md-12">
        <?php if ($personInfo['loggedIn'] && $personInfo['details']['person_id'] == $sketch['person_id']): ?>
          <h1 id="title" class="text-center">
            <a id="rename" href="/sketch/<?= $this->esc($sketch['sketch_id']); ?>/rename" onclick="showRename(event)">
              <?= $this->esc($sketch['title']) ?> <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
            </a>
          </h1>
          <form id="rename-form" class="form-horizontal" action="/sketch/<?= $this->esc($sketch['sketch_id']); ?>/rename" method="POST" style="display: none;">
            <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken'] ?>" />
            <input type="text" name="newTitle" id="newTitle" class="form-control" value="<?= $this->esc($sketch['title']); ?>" maxlength="100" required autofocus />
            <button class="btn btn-success renameSubmit" onclick="submitRename(event)"><i class="fa fa-paper-plane" aria-hidden="true"></i> Update Title</button>
            <button class="btn btn-danger renameCancel" onclick="cancelRename(event)"><i class="fa fa-window-close" aria-hidden="true"></i> Cancel</button>
          </form>
        <?php else: ?>
          <h1 class="text-center"><?= $this->esc($sketch['title']) ?></h1>
        <?php endif; ?>
      </h1>
      </div><!-- col-md-12 -->
    </div><!-- row -->


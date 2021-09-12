    <div class="row">
      <div class="col-md-12">
        <?php if ($personInfo['loggedIn'] && $personInfo['details']['person_id'] == $sketch['person_id']): ?>
          <h1 id="titleWithEdit" class="text-center">
            <span id="title"><?= $this->esc($sketch['title']) ?></span>
            <a id="rename" href="/sketch/<?= $this->esc($sketch['sketch_id']); ?>/rename">
              <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
            </a>
          </h1>
          <form id="rename-form" class="form-horizontal" action="/sketch/<?= $this->esc($sketch['sketch_id']); ?>/rename" method="POST" style="display: none;">
            <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken'] ?>" />
            <input type="text" name="newTitle" id="newTitle" class="form-control" value="<?= $this->esc($sketch['title']); ?>" maxlength="100" required autofocus />
            <button id="renameSubmit" class="btn btn-success renameSubmit"><i class="fa fa-paper-plane" aria-hidden="true"></i> Update Title</button>
            <button id="renameCancel" class="btn btn-danger renameCancel"><i class="fa fa-window-close" aria-hidden="true"></i> Cancel</button>
          </form>
        <?php else: ?>
          <h1 class="text-center"><?= $this->esc($sketch['title']) ?></h1>
        <?php endif; ?>
      </div><!-- col-md-12 -->
    </div><!-- row -->


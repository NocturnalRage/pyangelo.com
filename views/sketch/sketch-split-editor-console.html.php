    <?php if ($layout == 'cols') : ?>
      <div id="editorWrapper" class="grid-cols">
    <?php else : ?>
      <div id="editorWrapper" class="grid-rows">
    <?php endif; ?>
      <div id="editor" data-sketch-id="<?= $this->esc($sketch['sketch_id']); ?>" data-crsf-token="<?= $personInfo['crsfToken'] ?>" data-layout="<?= $layout ?>" data-read-only="<?= $personInfo['loggedIn'] && $personInfo['details']['person_id'] == $sketch['person_id'] ? 0 : 1 ?>"></div>
      <?php if ($layout == 'cols') : ?>
        <div class="gutter-col-1"></div>
      <?php else : ?>
        <div class="gutter-row-1"></div>
      <?php endif; ?>
      <div>
        <pre id="console"></pre>
      </div>
    </div><!-- editorWrapper grid -->

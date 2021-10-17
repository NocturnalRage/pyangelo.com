    <?php if ($sketch['layout'] == 'cols') : ?>
      <div id="editorWrapper" class="grid-cols">
    <?php else : ?>
      <div id="editorWrapper" class="grid-rows">
    <?php endif; ?>
      <div id="editorImagePreview"></div>
      <div id="editorAudioPreview"></div>
      <div id="editor" data-sketch-id="<?= $this->esc($sketch['sketch_id']); ?>" data-crsf-token="<?= $personInfo['crsfToken'] ?>" data-layout="<?= $sketch['layout'] ?>" data-read-only="<?= $personInfo['loggedIn'] && $personInfo['details']['person_id'] == $sketch['person_id'] ? 0 : 1 ?>"></div>
      <?php if ($sketch['layout'] == 'cols') : ?>
        <div id="editorGutter" class="gutter-col-1"></div>
      <?php else : ?>
        <div id="editorGutter" class="gutter-row-1"></div>
      <?php endif; ?>
      <pre id="console"></pre>
    </div><!-- editorWrapper grid -->

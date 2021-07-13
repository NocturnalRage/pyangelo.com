    <!-- editor -->
    <div class="row">
      <div class="col-md-12" id="fileTabs">
        <span class="editorTab" onclick="newPythonFile();">+</span>
      </div><!-- col-md-12 -->
    </div><!-- row -->
    <div class="row">
      <div class="col-md-12">
      <div id="editor" data-sketch-id="<?= $this->esc($sketch['sketch_id']); ?>" data-crsf-token="<?= $personInfo['crsfToken'] ?>" data-read-only="<?= $personInfo['loggedIn'] && $personInfo['details']['person_id'] == $sketch['person_id'] ? 0 : 1 ?>"></div>
      </div><!-- col-md-12 -->
    </div><!-- row -->


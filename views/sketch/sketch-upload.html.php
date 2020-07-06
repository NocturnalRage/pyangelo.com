<div class="row">
  <div class="col-md-12 text-center">
    <h1>Upload an Image or Sound</h1>
    <div id="dropzoneImage" class="dropzone">
      <form class="image-form">
        <input id="crsfToken" type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken'] ?>" />
        <input id="sketchId" type="hidden" name="sketchId" value="<?= $sketch['sketch_id']; ?>" />
        <p>Upload multiple image files with the file dialog or by dragging and dropping images onto the dashed region</p>
        <input type="file" id="assetUpload" multiple accept="image/*, audio/*" class="visually-hidden">
        <label class="btn btn-primary" for="assetUpload">Select some files</label>
      </form>
      <div id="gallery"></div>
    </div>
  </div><!-- col-md-12 -->
</div><!-- row -->
<script src="<?= mix('js/dropzone.js'); ?>"></script>

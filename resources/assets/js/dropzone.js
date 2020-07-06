window.addEventListener("drop",function(e){ e.preventDefault(); });
window.addEventListener("dragover",function(e){ e.preventDefault(); });

let assetUpload = document.getElementById('assetUpload');
let dropzoneImage = document.getElementById('dropzoneImage');
let dropzoneSound = document.getElementById('dropzoneSound');

assetUpload.addEventListener('change', handleChangeFiles);
dropzoneImage.addEventListener('dragenter', highlightDropzone);
dropzoneImage.addEventListener('dragover', highlightDropzone);
dropzoneImage.addEventListener('dragleave', unhighlightDropzone);
dropzoneImage.addEventListener('drop', unhighlightDropzone);
dropzoneImage.addEventListener('drop', handleImageDrop);

function highlightDropzone(event) {
  event.preventDefault();
  dropzoneImage.style.backgroundColor = '#CCCCCC';
}
function unhighlightDropzone(event) {
  event.preventDefault();
  dropzoneImage.style.backgroundColor = '#EEEEEE';
}
function handleImageDrop(event) {
  event.preventDefault();
  let dt = event.dataTransfer;
  let files = dt.files;
  handleFiles(files);
}
function handleChangeFiles() {
  const files = this.files;
  handleFiles(files);
}
function handleFiles(files) {
  files = [...files];
  files.forEach(uploadFile);
}

function uploadFile(file) {
  let data = new FormData()

  data.append('file', file)
  data.append('crsfToken', document.getElementById('crsfToken').value);
  data.append('sketchId', document.getElementById('sketchId').value);

  fetch('/upload/asset', {
    method: 'POST',
    body: data
  })
  .then(response => response.json())
  .then(updateWebPage)
  .catch((error) => { console.error('Error: ', error); })
}

function updateWebPage(response) {
  if (response.status == "success") {
    let paraName = document.createElement('p');
    paraName.innerText = 'File uploaded: ' + response.filename;
    document.getElementById('gallery').appendChild(paraName);
    addTab(response);
  }
  else {
    let paraUploadError = document.createElement('p');
    paraUploadError.innerText = response.message;
    paraUploadError.style.color = "red";
    document.getElementById('gallery').appendChild(paraUploadError);
    console.log(response);
  }
}

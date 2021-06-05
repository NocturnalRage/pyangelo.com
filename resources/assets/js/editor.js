let editorSession = 0;
let currentSession = 0;
let currentFilename = "main.py";
let editor = ace.edit("editor");
editor.$blockScrolling = Infinity;

var UndoManager = ace.require("ace/undomanager").UndoManager;

var PythonMode = ace.require("ace/mode/python").Mode;
ace.require("ace/ext/language_tools");
var EditSession = require("ace/edit_session").EditSession;
var editSessions = [];

function getCode(session) {
  return editSessions[session].getValue();
}
function saveCode(filename) {
  const crsfToken = document.getElementById('editor').getAttribute('data-crsf-token');
  const data = "filename=" + encodeURIComponent(filename) + "&program=" + encodeURIComponent(getCode(currentSession)) + "&crsfToken=" + encodeURIComponent(crsfToken);
  const options = {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: data
  };
  const sketchId = document.getElementById('editor').getAttribute('data-sketch-id');
  fetch('/sketch/' + sketchId + '/save', options)
    .then(response => response.json())
    .then(data => {
      console.log(data);
    });
}
function loadCode() {
  const sketchId = document.getElementById('editor').getAttribute('data-sketch-id');
  fetch('/sketch/code/' + sketchId)
    .then(response => response.json())
    .then(setupEditor)
    .catch((error) => { console.error('Error: ', error); })
}
function setupEditor(response) {
  response.files.forEach(addTab);
  editor.setSession(editSessions[currentSession]);
}
function addTab(file) {
    let span = document.createElement('span');
    span.innerHTML = file.filename;
    span.classList.add("editorTab");
    fileTabs = document.getElementById('fileTabs');

    if (file.filename.endsWith(".py")) {
      // Sk.builtinFiles.files[file.filename] = file.sourceCode;
      const readOnly = document.getElementById('editor').getAttribute('data-read-only');
      editSessions.push(new EditSession(file.sourceCode));
      editSessions[editorSession].setMode(new PythonMode());
      editSessions[editorSession].setUndoManager(new UndoManager());
      if (readOnly == "true") {
        editor.setOptions({
            readOnly: true,
            fontSize: "11pt",
            enableBasicAutocompletion: true
        });
      }
      else {
        editor.setOptions({
            readOnly: false,
            fontSize: "11pt",
            enableBasicAutocompletion: true
        });
      }

      span.setAttribute("data-editor-session", editorSession);
      span.setAttribute("data-filename", file.filename);
      span.onclick=loadSession;
      editorSession++;
    }
    fileTabs.appendChild(span);
}
function loadSession(ev) {
  saveCode(currentFilename);
  currentFilename = ev.target.getAttribute("data-filename");
  currentSession = ev.target.getAttribute("data-editor-session");
  editor.setSession(editSessions[currentSession]);
}
function newPythonFile() {
  // TODO: Check if filename already exists.
  let filename = window.prompt("Enter the new filename: ");
  if (!filename.endsWith(".py")) {
    alert("The filename must end with .py");
    return;
  }
  const sketchId = document.getElementById('editor').getAttribute('data-sketch-id');
  const crsfToken = document.getElementById('editor').getAttribute('data-crsf-token');
  const data = "filename=" + encodeURIComponent(filename) + "&sketchId=" + encodeURIComponent(sketchId) + "&crsfToken=" + encodeURIComponent(crsfToken);
  const options = {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: data
  };
  fetch('/sketch/' + sketchId +'/addFile', options)
    .then(response => response.json())
    .then(addNewFile)
    .catch((error) => { console.error('Error: ', error); })
}
function addNewFile(response) {
  let file = { "filename": response.filename, "sourceCode": "" };
  addTab(file);
}
function showRename(event) {
  event.preventDefault();
  document.getElementById('rename-form').style.display = "block";
  document.getElementById('title').style.display = "none";
}
function cancelRename(event) {
  event.preventDefault();
  document.getElementById('rename-form').style.display = "none";
  document.getElementById('title').style.display = "block";
}
function submitRename(event) {
  event.preventDefault();
  const sketchId = document.getElementById('editor').getAttribute('data-sketch-id');
  const crsfToken = document.getElementById('editor').getAttribute('data-crsf-token');
  const data = "newTitle=" + document.getElementById('newTitle').value + "&sketchId=" + encodeURIComponent(sketchId) + "&crsfToken=" + encodeURIComponent(crsfToken);
  const options = {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: data
  };
  fetch('/sketch/' + sketchId +'/rename', options)
    .then(response => response.json())
    .then(updateTitle)
    .catch((error) => { console.error('Error: ', error); })
}
function updateTitle(response) {
  const sketchId = document.getElementById('editor').getAttribute('data-sketch-id');
  document.getElementById('rename-form').style.display = "none";
  const innerHTML = '<a id="rename" href="/sketch/' + sketchId + '/rename" onclick="showRename(event)">' + response.title + ' <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
  document.getElementById('title').innerHTML = innerHTML;
  document.getElementById('title').style.display = "block";
  document.title = response.title;
}

function saveThenRun() {
  saveCode(currentFilename);
  const startStopButton = document.getElementById('startStop');
  startStopButton.removeEventListener('click', saveThenRun, false);
  startStopButton.style.backgroundColor = '#880000';
  startStopButton.textContent = 'Stop';
  startStopButton.addEventListener('click', saveThenStop, false);
  document.getElementById("console").innerHTML = '';
  // Calls runSkulpt defined in show.html for a sketch
  _stopExecution = false;
  runSkulpt(getCode(0));
}
function saveThenStop() {
  saveCode(currentFilename);
  stopCode();
}
function stopCode() {
  _stopExecution = true;
  Sk.builtin.stopAllSounds();
  const startStopButton = document.getElementById('startStop');
  startStopButton.removeEventListener('click', saveThenStop, false);
  startStopButton.style.backgroundColor = '#008800';
  startStopButton.textContent = 'Start';
  startStopButton.addEventListener('click', saveThenRun, false);
}

document.getElementById('startStop').addEventListener('click', saveThenRun, false);

loadCode();

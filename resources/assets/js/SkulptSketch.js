import { runSkulpt, stopSkulpt, debugSkulpt } from './SkulptSetup'
import { Editor } from './EditorSetup'
const Sk = require('skulpt')

const editorWindow = document.getElementById('editor')
const crsfToken = editorWindow.getAttribute('data-crsf-token')
const sketchId = editorWindow.getAttribute('data-sketch-id')
const isReadOnly = (editorWindow.getAttribute('data-read-only') === '1')
const fileTabs = document.getElementById('fileTabs')
const addNewFileTab = document.getElementById('addNewFileTab')
const showRenameLink = document.getElementById('rename')
const renameSubmitButton = document.getElementById('renameSubmit')
const renameCancelButton = document.getElementById('renameCancel')

if (!isReadOnly) {
  addNewFileTab.addEventListener('click', newPythonFile)
  if (!(showRenameLink === null)) {
    showRenameLink.addEventListener('click', showRename)
  }
  if (!(renameSubmitButton === null)) {
    renameSubmitButton.addEventListener('click', submitRename)
  }
  if (!(renameCancelButton === null)) {
    renameCancelButton.addEventListener('click', cancelRename)
  }
}

const aceEditor = new Editor(sketchId, crsfToken, Sk, fileTabs, isReadOnly)
Sk.PyAngelo.aceEditor = aceEditor
aceEditor.loadCode()
aceEditor.monitorErrorsOnChange()
aceEditor.listenForBreakPoints()

const startStopButton = document.getElementById('startStop')
startStopButton.addEventListener('click', saveThenRun)
const stepIntoButton = document.getElementById('stepInto')
stepIntoButton.addEventListener('click', debugSkulpt)
const stepOverButton = document.getElementById('stepOver')
stepOverButton.addEventListener('click', debugSkulpt)
const slowMotionButton = document.getElementById('slowMotion')
slowMotionButton.addEventListener('click', debugSkulpt)
const continueButton = document.getElementById('continue')
continueButton.addEventListener('click', debugSkulpt)

function saveThenRun () {
  if (!isReadOnly) {
    aceEditor.saveCurrentFile()
  }
  startStopButton.removeEventListener('click', saveThenRun, false)
  startStopButton.style.backgroundColor = '#880000'
  startStopButton.textContent = 'Stop'
  startStopButton.addEventListener('click', saveThenStop, false)
  Sk.PyAngelo.console.innerHTML = ''
  const debugging = document.getElementById('debug').checked
  runSkulpt(aceEditor.getCode(0), debugging, saveThenStop)
}

function saveThenStop () {
  if (!isReadOnly) {
    aceEditor.saveCurrentFile()
  }
  stopSkulpt()
  startStopButton.removeEventListener('click', saveThenStop, false)
  startStopButton.style.backgroundColor = '#008800'
  startStopButton.textContent = 'Start'
  startStopButton.addEventListener('click', saveThenRun, false)
}

function newPythonFile () {
  const moduleNames = [
    '_abcoll',
    'abc',
    'aifc',
    'antigravity',
    'anydbm',
    'array',
    'ast',
    'asynchat',
    'asyncore',
    'atexit',
    'audiodev',
    'base64',
    'BaseHTTPServer',
    'Bastion',
    'bdb',
    'binhex',
    'bisect',
    'bsddb',
    'calendar',
    'CGIHTTPServer',
    'cgi',
    'cgitb',
    'chunk',
    'cmd',
    'codecs',
    'codeop',
    'code',
    'collections',
    'colorsys',
    'commands',
    'compileall',
    'compiler',
    'config',
    'ConfigParser',
    'contextlib',
    'cookielib',
    'Cookie',
    'copy',
    'copy_reg',
    'cProfile',
    'csv',
    'ctypes',
    'curses',
    'datetime',
    'dbhash',
    'decimal',
    'difflib',
    'dircache',
    'dis',
    'distutils',
    'doctest',
    'document',
    'DocXMLRPCServer',
    'dumbdbm',
    'dummy_threading',
    'dummy_thread',
    'email',
    'encodings',
    'filecmp',
    'fileinput',
    'fnmatch',
    'formatter',
    'fpformat',
    'fractions',
    'ftplib',
    'functools',
    '__future__',
    'genericpath',
    'getopt',
    'getpass',
    'gettext',
    'glob',
    'gzip',
    'hashlib',
    'heapq',
    'hmac',
    'hotshot',
    'htmlentitydefs',
    'htmllib',
    'HTMLParser',
    'httplib',
    'idlelib',
    'ihooks',
    'image',
    'imaplib',
    'imghdr',
    'imputil',
    'io',
    'itertools',
    'json',
    'keyword',
    'lib2to3',
    'lib-dynload',
    'lib-tk',
    'linecache',
    'locale',
    'logging',
    '_LWPCookieJar',
    'macpath',
    'macurl2path',
    'mailbox',
    'mailcap',
    'markupbase',
    'math',
    'md5',
    'mhlib',
    'mimetools',
    'mimetypes',
    'MimeWriter',
    'mimify',
    'modulefinder',
    '_MozillaCookieJar',
    'multifile',
    'multiprocessing',
    'mutex',
    'netrc',
    'new',
    'nntplib',
    'ntpath',
    'nturl2path',
    'numbers',
    'opcode',
    'operator',
    'optparse',
    'os2emxpath',
    'os',
    'pdb',
    '__phello__.foo',
    'pickle',
    'pickletools',
    'pipes',
    'pkgutil',
    'platform',
    'platform',
    'plistlib',
    'popen2',
    'poplib',
    'posixfile',
    'posixpath',
    'pprint',
    'processing',
    'profile',
    'pstats',
    'pty',
    'pyclbr',
    'py_compile',
    'pydoc',
    'pydoc_topics',
    'pythonds',
    'Queue',
    'quopri',
    'random',
    're',
    'repr',
    'rexec',
    'rfc822',
    'rlcompleter',
    'robotparser',
    'runpy',
    'sched',
    'sets',
    'sgmllib',
    'sha',
    'shelve',
    'shlex',
    'shutil',
    'signal',
    'SimpleHTTPServer',
    'SimpleXMLRPCServer',
    'site',
    'smtpd',
    'smtplib',
    'sndhdr',
    'socket',
    'SocketServer',
    'sprite',
    'sqlite3',
    'sre_compile',
    'sre_constants',
    'sre_parse',
    'sre',
    'ssl',
    'stat',
    'statvfs',
    'StringIO',
    'string',
    'stringold',
    'stringprep',
    '_strptime',
    'struct',
    'subprocess',
    'sunaudio',
    'sunau',
    'symbol',
    'symtable',
    'tabnanny',
    'tarfile',
    'telnetlib',
    'tempfile',
    'test',
    'textwrap',
    'this',
    '_threading_local',
    'threading',
    'timeit',
    'time',
    'toaiff',
    'tokenize',
    'token',
    'traceback',
    'trace',
    'tty',
    'turtle',
    'types',
    'unittest',
    'urllib',
    'urllib2',
    'urlparse',
    'UserDict',
    'UserList',
    'user',
    'UserString',
    'uuid',
    'uu',
    'warnings',
    'wave',
    'weakref',
    'webbrowser',
    'webgl',
    'whichdb',
    'wsgiref',
    'xdrlib',
    'xml',
    'xmllib',
    'xmlrpclib',
    'zipfile'
  ]
  const filename = window.prompt('Enter the new filename: ')
  if (filename == null) {
    return
  }
  if (!filename.endsWith('.py')) {
    alert('The filename must end with .py')
    return
  }
  if (filename === 'main.py') {
    alert('You cannot create a second main.py. Please choose a different name for your file')
    return
  }
  const moduleName = filename.substr(0, filename.lastIndexOf('.'))
  if (moduleNames.includes(moduleName)) {
    alert(moduleName + ' is a system module. Please choose a different name for your file')
    return
  }
  // Check file not already created for this sketch
  if (Sk.builtinFiles.files['./' + filename] !== undefined) {
    alert('You have already created a file called ' + filename + ' for this sketch.')
  }
  aceEditor.addNewFile(filename)
}

function showRename (event) {
  event.preventDefault()
  document.getElementById('rename-form').style.display = 'block'
  document.getElementById('titleWithEdit').style.display = 'none'
}

function submitRename (event) {
  event.preventDefault()
  const data = 'newTitle=' + document.getElementById('newTitle').value + '&sketchId=' + encodeURIComponent(sketchId) + '&crsfToken=' + encodeURIComponent(crsfToken)
  const options = {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: data
  }
  fetch('/sketch/' + sketchId + '/rename', options)
    .then(response => response.json())
    .then(data => {
      if (data.status !== 'success') {
        throw new Error(data.message)
      }
      updateTitle(data)
    })
    .catch((error) => { console.error(error) })
}

function updateTitle (data) {
  document.getElementById('rename-form').style.display = 'none'
  document.getElementById('title').innerHTML = data.title
  document.getElementById('titleWithEdit').style.display = 'block'
  document.title = data.title
}

function cancelRename (event) {
  event.preventDefault()
  document.getElementById('rename-form').style.display = 'none'
  document.getElementById('title').style.display = 'block'
}

window.addEventListener('drop', function (e) { e.preventDefault() })
window.addEventListener('dragover', function (e) { e.preventDefault() })

const assetUpload = document.getElementById('assetUpload')
const dropzoneImage = document.getElementById('dropzoneImage')

assetUpload.addEventListener('change', handleChangeFiles)
dropzoneImage.addEventListener('dragenter', highlightDropzone)
dropzoneImage.addEventListener('dragover', highlightDropzone)
dropzoneImage.addEventListener('dragleave', unhighlightDropzone)
dropzoneImage.addEventListener('drop', unhighlightDropzone)
dropzoneImage.addEventListener('drop', handleImageDrop)

function highlightDropzone (event) {
  event.preventDefault()
  dropzoneImage.style.backgroundColor = '#CCCCCC'
}
function unhighlightDropzone (event) {
  event.preventDefault()
  dropzoneImage.style.backgroundColor = '#EEEEEE'
}
function handleImageDrop (event) {
  event.preventDefault()
  const dt = event.dataTransfer
  const files = dt.files
  handleFiles(files)
}
function handleChangeFiles () {
  const files = this.files
  handleFiles(files)
}
function handleFiles (files) {
  files = [...files]
  files.forEach(uploadFile)
}

function uploadFile (file) {
  const data = new FormData()

  data.append('file', file)
  data.append('crsfToken', document.getElementById('crsfToken').value)
  data.append('sketchId', document.getElementById('sketchId').value)

  fetch('/upload/asset', {
    method: 'POST',
    body: data
  })
    .then(response => response.json())
    .then(updateWebPage)
    .catch((error) => { console.error('Error: ', error) })
}

function updateWebPage (response) {
  if (response.status === 'success') {
    const paraName = document.createElement('p')
    paraName.innerText = 'File uploaded: ' + response.filename
    document.getElementById('gallery').appendChild(paraName)
    aceEditor.addTab(response)
    assetUpload.value = ''
  } else {
    const paraUploadError = document.createElement('p')
    paraUploadError.innerText = response.message
    paraUploadError.style.color = 'red'
    document.getElementById('gallery').appendChild(paraUploadError)
  }
}

const onresize = (domElem, callback) => {
  const resizeObserver = new ResizeObserver(() => callback())
  resizeObserver.observe(domElem)
}

onresize(editorWindow, function () {
  aceEditor.resize()
})

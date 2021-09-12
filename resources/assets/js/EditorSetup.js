import ace from 'ace'
import { staticWordCompleter } from './editorWordCompletion'

export class Editor {
  constructor (sketchId, crsfToken, Sk, fileTabs, isReadOnly) {
    this.sketchId = sketchId
    this.crsfToken = crsfToken
    this.Sk = Sk
    this.fileTabs = fileTabs
    this.isReadOnly = isReadOnly

    this.currentSession = 0
    this.currentFilename = 'main.py'
    ace.config.set('basePath', 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/')
    this.editor = ace.edit('editor')
    this.editor.$blockScrolling = Infinity
    this.editor.setTheme('ace/theme/dracula')
    this.editor.setOptions({
      readOnly: this.isReadOnly,
      fontSize: '12pt',
      enableBasicAutocompletion: true,
      enableSnippets: false,
      enableLiveAutocompletion: true
    })
    this.EditSession = ace.require('ace/edit_session').EditSession
    this.UndoManager = ace.require('ace/undomanager').UndoManager
    this.PythonMode = ace.require('ace/mode/python').Mode
    this.langTools = ace.require('ace/ext/language_tools')
    // which one is needed?
    this.langTools.setCompleters([staticWordCompleter])
    this.editor.completers = [staticWordCompleter]
    this.editSessions = []
  }

  addSession (code) {
    let index = this.editSessions.push(new this.EditSession(code))
    index--
    this.editSessions[index].setMode(new this.PythonMode())
    this.editSessions[index].setUndoManager(new this.UndoManager())
    return index
  }

  replaceSession (index, code) {
    this.editSessions[index].setValue(code)
  }

  setSession (index) {
    this.editor.setSession(this.editSessions[index])
  }

  gotoLine (lineNo) {
    this.editor.gotoLine(lineNo)
  }

  getCode (session) {
    return this.editSessions[session].getValue()
  }

  saveCurrentFile () {
    this.saveCode(this.currentFilename)
  }

  saveCode (filename) {
    const code = this.getCode(this.currentSession)
    if (filename !== 'main.py') {
      this.Sk.builtinFiles.files['./' + filename] = code
    }
    const data = 'filename=' + encodeURIComponent(filename) + '&program=' + encodeURIComponent(code) + '&crsfToken=' + encodeURIComponent(this.crsfToken)
    const options = {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: data
    }
    fetch('/sketch/' + this.sketchId + '/save', options)
      .then(response => response.json())
      .then(data => { console.log(data) })
  }

  loadCode () {
    fetch('/sketch/code/' + this.sketchId)
      .then(response => response.json())
      .then(data => {
        if (data.status !== 'success') {
          throw new Error(data.message)
        }
        this.setupEditor(data)
      })
      .catch(error => { console.error(error) })
  }

  setupEditor (data) {
    for (let i = 0; i < data.files.length; i++) {
      this.addTab(data.files[i])
    }
    this.setSession(this.currentSession)
  }

  addTab (file) {
    const closureEditor = this
    const span = document.createElement('span')
    span.dataset.filename = file.filename
    const text = document.createTextNode(file.filename)
    span.appendChild(text)
    if (file.filename !== 'main.py' && !this.isReadOnly) {
      const deleteButton = document.createElement('span')
      deleteButton.innerHTML = '&times;'
      deleteButton.onclick = function (ev) {
        ev.stopPropagation()
        if (confirm('Are you sure you want to delete ' + file.filename + '? This operation cannot be undone!')) {
          if (closureEditor.currentFilename === file.filename) {
            closureEditor.currentSession = 0
            closureEditor.setSession(closureEditor.currentSession)
            document.querySelector(".editorTab[data-filename='main.py']").classList.add('current')
          }
          closureEditor.deleteFile(file.filename)
          delete closureEditor.Sk.builtinFiles.files['./' + file.filename]
        }
      }
      deleteButton.classList.add('smallButton')
      span.appendChild(deleteButton)
    }
    span.classList.add('editorTab')
    if (file.filename === 'main.py') {
      span.classList.add('current')
    }

    if (file.filename.endsWith('.py')) {
      if (!file.sourceCode) {
        file.sourceCode = ''
      }
      if (file.filename !== 'main.py') {
        this.Sk.builtinFiles.files['./' + file.filename] = file.sourceCode
      }
      const sessionIndex = this.addSession(file.sourceCode)
      span.setAttribute('data-editor-session', sessionIndex)
      span.setAttribute('data-filename', file.filename)
      span.onclick = function (ev) {
        if (!closureEditor.isReadOnly) {
          closureEditor.saveCode(closureEditor.currentFilename)
        }
        closureEditor.currentFilename = ev.target.getAttribute('data-filename')
        closureEditor.currentSession = ev.target.getAttribute('data-editor-session')
        closureEditor.editor.setSession(closureEditor.editSessions[closureEditor.currentSession])
        document.querySelector('.editorTab.current').classList.remove('current')
        ev.target.classList.add('current')
      }
    }
    this.fileTabs.appendChild(span)
  }

  addNewFile (filename) {
    const data = 'filename=' + encodeURIComponent(filename) + '&crsfToken=' + encodeURIComponent(this.crsfToken)
    const options = {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: data
    }
    fetch('/sketch/' + this.sketchId + '/addFile', options)
      .then(response => response.json())
      .then(data => {
        if (data.status !== 'success') {
          throw new Error(data.message)
        }
        const file = { filename: data.filename, sourceCode: '' }
        this.addTab(file)
        this.currentFilename = file.filename
        this.currentSession = this.editSessions.length - 1
        this.setSession(this.currentSession)
        document.querySelector('.editorTab.current').classList.remove('current')
        document.querySelector(`.editorTab[data-filename='${file.filename}']`).classList.add('current')
      })
      .catch(error => { console.error(error) })
  }

  deleteFile (filename) {
    const data = 'filename=' + encodeURIComponent(filename) + '&sketchId=' + encodeURIComponent(this.sketchId) + '&crsfToken=' + encodeURIComponent(this.crsfToken)
    const options = {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: data
    }
    fetch('/sketch/' + this.sketchId + '/deleteFile', options)
      .then(response => response.json())
      .then(data => {
        if (data.status !== 'success') {
          throw new Error(data.message)
        }
        this.removeTab(data)
      })
      .catch(error => { console.error(error) })
  }

  removeTab (data) {
    const span = document.querySelector(`.editorTab[data-filename='${data.filename}']`)
    if (!span) {
      alert('An unknown error occured; please try again or contact us.')
      return
    }
    span.remove()
  }

  resize () {
    this.editor.resize()
  }
}

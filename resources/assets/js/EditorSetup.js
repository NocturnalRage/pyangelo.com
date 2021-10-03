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

  clearAllAnnotations () {
    for (const session in this.editSessions) {
      this.editSessions[session].clearAnnotations()
    }
  }

  monitorErrorsOnChange () {
    const closureEditor = this
    this.editor.on('change', function (delta) {
      closureEditor.editSessions[closureEditor.currentSession].clearAnnotations()
      closureEditor.Sk.configure({
        __future__: closureEditor.Sk.python3
      })
      try {
        closureEditor.Sk.compile(
          closureEditor.getCode(closureEditor.currentSession),
          closureEditor.currentFilename,
          'exec',
          true
        )
      } catch (err) {
        if (err.traceback) {
          const lineno = err.traceback[0].lineno
          const colno = err.traceback[0].colno
          let errorMessage
          if (err.message) {
            errorMessage = err.message
          } else if (err.nativeError) {
            errorMessage = err.nativeError.message
          } else {
            errorMessage = err.toString()
          }
          closureEditor.editSessions[closureEditor.currentSession].setAnnotations([{
            row: lineno - 1,
            column: colno,
            text: errorMessage,
            type: 'error'
          }])
        }
      }
    })
  }

  listenForBreakPoints () {
    const closureEditor = this
    this.editor.on('guttermousedown', function (e) {
      const target = e.domEvent.target

      if (target.className.indexOf('ace_gutter-cell') === -1) {
        return
      }

      if (!closureEditor.editor.isFocused()) {
        return
      }

      if (e.clientX > 25 + target.getBoundingClientRect().left) {
        return
      }

      const row = e.getDocumentPosition().row
      const breakpoints = closureEditor.editSessions[closureEditor.currentSession].getBreakpoints(row, 0)

      // If there's a breakpoint already defined, it should be removed, offering the toggle feature
      if (typeof breakpoints[row] === typeof undefined) {
        closureEditor.editSessions[closureEditor.currentSession].setBreakpoint(row)
      } else {
        closureEditor.editSessions[closureEditor.currentSession].clearBreakpoint(row)
      }
    })
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

  gotoLine (lineNo, colNo = 0, animate = true) {
    this.editor.gotoLine(lineNo, colNo, animate)
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
        closureEditor.setSession(closureEditor.currentSession)
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

  setReadOnly (readOnly) {
    this.editor.setReadOnly(readOnly)
  }

  resize () {
    this.editor.resize()
  }
}

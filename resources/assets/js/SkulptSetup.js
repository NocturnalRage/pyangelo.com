import { populateTable } from './makeTable'
const Sk = require('skulpt')

let receivingInput = false
Sk.PyAngelo.preparePage()

// Allow skulpt script to be stopped
let _stopExecution = false

// For debugging
const debugActions = {
  WAIT: 0,
  STEPINTO: 1,
  STEPOVER: 2,
  SLOWMOTION: 3,
  CONTINUE: 4
}
let debugAction = debugActions.WAIT
const debugTableBody = document.getElementById('debugTableBody')

function createColouredTextSpanElement (text) {
  const spanElement = document.createElement('span')
  spanElement.appendChild(document.createTextNode(text))
  spanElement.style.color = Sk.PyAngelo.textColour
  spanElement.style.backgroundColor = Sk.PyAngelo.highlightColour
  spanElement.style.fontSize = Sk.PyAngelo.textSize
  spanElement.style.lineHeight = '1.5em'
  spanElement.style.margin = '0px'
  let padding = Sk.PyAngelo.textSize
  padding = parseInt(padding)
  padding = 0.25 * padding + 'px'
  spanElement.style.paddingBottom = padding
  spanElement.style.paddingTop = padding
  return spanElement
}

function outf (text) {
  Sk.PyAngelo.console.appendChild(createColouredTextSpanElement(text))
  Sk.PyAngelo.console.scrollTop = Sk.PyAngelo.console.scrollHeight
}

function logError (message) {
  const tc = Sk.PyAngelo.textColour
  const hc = Sk.PyAngelo.highlightColour
  Sk.PyAngelo.textColour = 'rgba(255, 0, 0, 1)'
  Sk.PyAngelo.highlightColour = 'rgba(255, 255, 255, 1)'
  outf(message)
  Sk.PyAngelo.textColour = tc
  Sk.PyAngelo.highlightColour = hc
}

export function debugSkulpt (event) {
  if (event.target.id === 'stepInto') {
    debugAction = debugActions.STEPINTO
  } else if (event.target.id === 'stepOver') {
    debugAction = debugActions.STEPOVER
  } else if (event.target.id === 'slowMotion') {
    debugAction = debugActions.SLOWMOTION
  } else if (event.target.id === 'continue') {
    debugAction = debugActions.CONTINUE
  }
}

function hideDebuggingUI () {
  if (Sk.debugging) {
    const debugTableWrapper = document.getElementById('debugTableWrapper')
    debugTableWrapper.style.display = 'none'
    const debugButtons = document.getElementById('debugButtons')
    debugButtons.style.display = 'none'
  }
}

export function stopSkulpt () {
  _stopExecution = true
  if (receivingInput) {
    hideDebuggingUI()
    Sk.PyAngelo.aceEditor.restoreReadOnly()
    receivingInput = false
    outf('\n')
    logError('Program Stopped!\n')
  }
  if (Sk.debugging) {
    debugAction = debugActions.CONTINUE
  }
  Sk.builtin.stopAllSounds()
}

export function runSkulpt (code, debugging, stopFunction) {
  (Sk.TurtleGraphics || (Sk.TurtleGraphics = {})).target = 'turtleDiv'
  Sk.PyAngelo.aceEditor.setReadOnly(true)
  _stopExecution = false
  if (debugging) {
    debugAction = debugActions.WAIT
    const debugTableWrapper = document.getElementById('debugTableWrapper')
    debugTableWrapper.style.display = 'block'
    const debugButtons = document.getElementById('debugButtons')
    debugButtons.style.display = 'block'
  }
  Sk.PyAngelo.aceEditor.clearAllAnnotations()
  Sk.PyAngelo.ctx.save()
  Sk.PyAngelo.reset()

  Sk.inputfun = function (prompt) {
    receivingInput = true
    return new Promise(function (resolve, reject) {
      const inputElement = document.createElement('span')
      inputElement.setAttribute('contenteditable', 'true')
      inputElement.style.backgroundColor = Sk.PyAngelo.highlightColour
      inputElement.style.color = Sk.PyAngelo.textColour
      inputElement.style.outlineStyle = 'none'
      inputElement.id = 'inputElement'
      Sk.PyAngelo.console.appendChild(inputElement)
      inputElement.focus()
      inputElement.addEventListener('keyup', function (e) {
        e.preventDefault()
        if (e.key === 'Enter') {
          const userResponse = inputElement.innerText.replace(/\n+$/, '')
          inputElement.remove()
          outf(userResponse)
          outf('\n')
          receivingInput = false
          resolve(userResponse)
        }
      })
    })
  }

  Sk.configure({
    output: outf,
    inputfunTakesPrompt: false,
    debugging: debugging,
    killableWhile: true,
    killableFor: false,
    killableForever: true,
    __future__: Sk.python3
  })

  Sk.onBeforeImport = function () {
    return Sk.misceval.promiseToSuspension(new Promise(function (resolve, reject) {
      setTimeout(function () {
        resolve()
      }, 10)
    }))
  }

  function sleep (ms) {
    return new Promise(resolve => setTimeout(resolve, ms))
  }

  async function lineStepper (susp) {
    const breakPoints = Sk.PyAngelo.aceEditor.editor.getSession().getBreakpoints()
    try {
      checkForStop()
      let child = susp.child
      if (debugAction === debugActions.STEPINTO) {
        while (child.child.child != null) {
          child = child.child
        }
      }
      if (currentLineNo === child.$lineno) {
        return Promise.resolve(susp.resume())
      }
      currentLineNo = child.$lineno
      const shouldBreak = debugAction !== debugActions.CONTINUE || breakPoints[currentLineNo - 1] === 'ace_breakpoint'
      if (!shouldBreak) {
        return Promise.resolve(susp.resume())
      }
      let filename = child.$filename
      if (filename === '<stdin>.py') {
        filename = 'main.py'
        Sk.PyAngelo.aceEditor.gotoLine(currentLineNo)
      } else {
        filename = filename.substring(filename.lastIndexOf('/') + 1)
      }
      const editSession = document.querySelector(".editorTab[data-filename='" + filename + "']")
      if (editSession !== null) {
        document.querySelector('.editorTab.current').classList.remove('current')
        editSession.classList.add('current')
        Sk.PyAngelo.aceEditor.currentSession = editSession.getAttribute('data-editor-session')
        Sk.PyAngelo.aceEditor.currentFilename = editSession.getAttribute('data-filename')
        Sk.PyAngelo.aceEditor.setSession(Sk.PyAngelo.aceEditor.currentSession)
        Sk.PyAngelo.aceEditor.gotoLine(currentLineNo)
      } else if (filename !== 'main.py') {
        /* this is because the playground does not have
         * editor tabs so if it is running the main program
         * we still want to debug it.
         * */
        return Promise.resolve(susp.resume())
      }
      const debugGlobals = {}
      const debugLocals = {}

      const globals = child.$gbl
      for (const global in globals) {
        if (globals[global] != null && !global.startsWith('__') && !global.startsWith('$')) {
          const datatype = Object.getPrototypeOf(globals[global]).tp$name
          if (datatype !== 'function' && datatype !== 'type' && datatype !== 'module') {
            const datavalue = globals[global].toString()
            const entry = {
              scope: 'global',
              value: datavalue,
              type: datatype
            }
            debugGlobals[global] = entry
          }
        }
      }
      const locals = child.$loc
      for (const local in locals) {
        if (locals[local] != null && !local.startsWith('__') && !local.startsWith('$')) {
          const datatype = Object.getPrototypeOf(locals[local]).tp$name
          if (datatype !== 'function' && datatype !== 'type' && datatype !== 'module') {
            const datavalue = locals[local].toString()
            const entry = {
              scope: 'global',
              value: datavalue,
              type: datatype
            }
            debugGlobals[local] = entry
          }
        }
      }
      const localsTmp = child.$tmps
      for (const local in localsTmp) {
        if (localsTmp[local] != null && !local.startsWith('__') && !local.startsWith('$')) {
          const datatype = Object.getPrototypeOf(localsTmp[local]).tp$name
          if (datatype !== 'function' && datatype !== 'type' && datatype !== 'module') {
            const datavalue = localsTmp[local].toString()
            const entry = {
              scope: 'local',
              value: datavalue,
              type: datatype
            }
            debugLocals[local] = entry
          }
        }
      }
      populateTable(debugTableBody, debugGlobals, debugLocals)

      if (debugAction === debugActions.SLOWMOTION) {
        await sleep(1000)
      } else {
        debugAction = debugActions.WAIT
      }
      while (debugAction === debugActions.WAIT) {
        await sleep(10)
      }
      return Promise.resolve(susp.resume())
    } catch (e) {
      return Promise.reject(e)
    }
  }

  function checkForStop () {
    if (_stopExecution) {
      Sk.builtin.stopAllSounds()
      throw new ProgramStoppedException('Program stopped!')
    }
  }

  function ProgramStoppedException (message) {
    this.message = message
    this.name = 'ProgramStopped'
  }

  let currentLineNo = 1
  const handlers = {}
  handlers['*'] = checkForStop

  if (Sk.debugging) {
    handlers['Sk.debug'] = lineStepper
    handlers['Sk.delay'] = lineStepper
    if (Sk.PyAngelo.aceEditor.currentSession !== 0) {
      const editSession = document.querySelector(".editorTab[data-filename='main.py']")
      if (editSession !== null) {
        document.querySelector('.editorTab.current').classList.remove('current')
        editSession.classList.add('current')
        Sk.PyAngelo.aceEditor.currentSession = editSession.getAttribute('data-editor-session')
        Sk.PyAngelo.aceEditor.currentFilename = editSession.getAttribute('data-filename')
        Sk.PyAngelo.aceEditor.setSession(Sk.PyAngelo.aceEditor.currentSession)
      }
    }

    Sk.PyAngelo.aceEditor.gotoLine(currentLineNo)
  }
  const skulptRunPromise = Sk.misceval.asyncToPromise(
    function () {
      return Sk.importMainWithBody('<stdin>', true, code, true)
    },
    handlers
  )

  skulptRunPromise.then(function (mod) {}, function (err) {
    let editorErrorMessage
    let consoleErrorMessage
    if (err.name === 'ProgramStopped') {
      editorErrorMessage = err.message
      consoleErrorMessage = err.message
    } else if (err.message) {
      editorErrorMessage = err.message
      consoleErrorMessage = err.message + '\n' + err.stack + '\n'
    } else if (err.nativeError) {
      editorErrorMessage = err.nativeError.message
      consoleErrorMessage = err.nativeError.message + '\n' + err.nativeError.stack + '\n'
    } else {
      editorErrorMessage = err.toString()
      consoleErrorMessage = err.toString() + '\n' + (err.stack || '') + '\n'
    }
    if (err.traceback) {
      let topMostFilename = err.traceback[0].filename
      if (topMostFilename === '<stdin>.py') {
        topMostFilename = 'main.py'
      } else {
        topMostFilename = topMostFilename.substring(topMostFilename.lastIndexOf('/') + 1)
      }
      editorErrorMessage += 'Error found in file ' + topMostFilename
      consoleErrorMessage += 'Error found in file ' + topMostFilename + '\n'
    }
    logError(consoleErrorMessage)

    let reportedError = false
    if (err.traceback) {
      for (let i = err.traceback.length - 1; i >= 0; i--) {
        const lineno = err.traceback[i].lineno
        const colno = err.traceback[i].colno
        let filename = err.traceback[i].filename
        if (filename === '<stdin>.py') {
          filename = 'main.py'
        } else {
          filename = filename.substring(filename.lastIndexOf('/') + 1)
        }
        const editSession = document.querySelector(".editorTab[data-filename='" + filename + "']")
        if (editSession !== null) {
          reportedError = true
          const errorSession = editSession.getAttribute('data-editor-session')
          Sk.PyAngelo.aceEditor.editSessions[errorSession].setAnnotations([{
            row: lineno - 1,
            column: colno,
            text: editorErrorMessage,
            type: 'error'
          }])
          document.querySelector('.editorTab.current').classList.remove('current')
          editSession.classList.add('current')
          Sk.PyAngelo.aceEditor.currentSession = editSession.getAttribute('data-editor-session')
          Sk.PyAngelo.aceEditor.currentFilename = editSession.getAttribute('data-filename')
          Sk.PyAngelo.aceEditor.setSession(Sk.PyAngelo.aceEditor.currentSession)
          Sk.PyAngelo.aceEditor.gotoLine(lineno, colno)
        }
      }
      // This might happen on playground
      // where there are no tabs
      if (reportedError === false) {
        const lineno = err.traceback[0].lineno
        const colno = err.traceback[0].colno
        Sk.PyAngelo.aceEditor.gotoLine(lineno, colno)
        Sk.PyAngelo.aceEditor.editSessions[0].setAnnotations([{
          row: lineno - 1,
          column: colno,
          text: editorErrorMessage,
          type: 'error'
        }])
      }
    }
  })
  skulptRunPromise.finally(function () {
    hideDebuggingUI()
    stopFunction()
    Sk.PyAngelo.aceEditor.restoreReadOnly()
    Sk.PyAngelo.ctx.restore()
  })
}

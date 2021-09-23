const Sk = require('skulpt')

Sk.PyAngelo.preparePage()

// Allow skulpt script to be stopped
let _stopExecution = false

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

export function stopSkulpt () {
  _stopExecution = true
  Sk.builtin.stopAllSounds()
}

export function runSkulpt (code, debugging, stopFunction) {
  _stopExecution = false
  Sk.PyAngelo.aceEditor.clearAllAnnotations()
  Sk.PyAngelo.ctx.save()
  Sk.PyAngelo.reset()

  Sk.inputfun = function (prompt) {
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

  let currentLineNo = 1
  async function lineStepper (susp) {
    try {
      let child = susp.child
      // traversing down to lowest child for a step-into
      // TODO: make this optional depending on 'step into' vs 'step over' modes
      while (child.child.child != null) {
        child = child.child
      }
      if (currentLineNo !== child.$lineno) {
        currentLineNo = child.$lineno
        checkForStop()
        await sleep(1000)
        Sk.PyAngelo.aceEditor.gotoLine(currentLineNo)
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

  let myPromise
  if (Sk.debugging) {
    Sk.PyAngelo.aceEditor.gotoLine(currentLineNo)
    myPromise = Sk.misceval.asyncToPromise(function () {
      return Sk.importMainWithBody('<stdin>', true, code, true)
    }, {
      // handle a suspension of the executing code
      // "*" says handle all types of suspensions
      '*': lineStepper
    })
  } else {
    myPromise = Sk.misceval.asyncToPromise(function () {
      return Sk.importMainWithBody('<stdin>', true, code, true)
    }, {
      // handle a suspension of the executing code
      // "*" says handle all types of suspensions
      '*': checkForStop
    })
  }

  myPromise.then(function (mod) {}, function (err) {
    const tc = Sk.PyAngelo.textColour
    const hc = Sk.PyAngelo.highlightColour
    Sk.PyAngelo.textColour = 'rgba(255, 0, 0, 1)'
    Sk.PyAngelo.highlightColour = 'rgba(255, 255, 255, 1)'
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
      editorErrorMessage += 'Error found in file ' + err.traceback[0].filename
      consoleErrorMessage += 'Error found in file ' + err.traceback[0].filename + '\n'
    }
    outf(consoleErrorMessage)
    Sk.PyAngelo.textColour = tc
    Sk.PyAngelo.highlightColour = hc
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
    }
  })
  myPromise.finally(function () { stopFunction() })
  Sk.PyAngelo.ctx.restore()
}

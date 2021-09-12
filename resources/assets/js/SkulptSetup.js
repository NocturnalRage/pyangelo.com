const Sk = require('skulpt')

Sk.preparePyAngeloPage()

// Allow skulpt script to be stopped
let _stopExecution = false

function createColouredTextSpanElement (text) {
  const spanElement = document.createElement('span')
  spanElement.appendChild(document.createTextNode(text))
  spanElement.style.color = Sk.PyAngelo.textColour
  spanElement.style.backgroundColor = Sk.PyAngelo.highlightColour
  spanElement.style.fontSize = Sk.PyAngelo.textSize
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

export function runSkulpt (code, stopFunction) {
  _stopExecution = false
  Sk.PyAngelo.ctx.save()
  Sk.resetPyAngelo()

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
    debugging: false,
    killableWhile: true,
    killableFor: false,
    __future__: Sk.python3
  })

  if (Sk.PyAngelo.debug.checked) {
    Sk.debugging = true
    console.log('Running in debug mode')
  } else {
    Sk.debugging = false
  }

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
    if (err.name === 'ProgramStopped') {
      outf(err.message + '\n')
    } else if (err.message) {
      outf(err.message + '\n')
      outf(err.stack)
    } else if (err.nativeError) {
      outf(err.nativeError.message + '\n')
      outf(err.nativeError.stack)
    } else {
      outf(err.toString())
      outf(err.stack || '')
    }
    Sk.PyAngelo.textColour = tc
    Sk.PyAngelo.highlightColour = hc
  })
  myPromise.finally(function () { stopFunction() })
  Sk.PyAngelo.ctx.restore()
}

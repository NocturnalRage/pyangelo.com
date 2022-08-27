import { runSkulpt, stopSkulpt, debugSkulpt } from './SkulptSetup'
import { Editor } from './EditorSetup'
import './editorLayout'
const Sk = require('skulpt')

// Only one session in standalone editor
const session = 0

const editorWindow = document.getElementById('editor')
const crsfToken = 'no-token-needed-in-editor'
const sketchId = 0
const isReadOnly = false
const fileTabs = null

const aceEditor = new Editor(sketchId, crsfToken, Sk, fileTabs, isReadOnly)
Sk.PyAngelo.aceEditor = aceEditor
const listenForErrors = true
const autosave = false
aceEditor.onChange(listenForErrors, autosave)
aceEditor.listenForBreakPoints()

const startStopButton = document.getElementById('startStop')
startStopButton.addEventListener('click', runCode)
const stepIntoButton = document.getElementById('stepInto')
stepIntoButton.addEventListener('click', debugSkulpt)
const stepOverButton = document.getElementById('stepOver')
stepOverButton.addEventListener('click', debugSkulpt)
const slowMotionButton = document.getElementById('slowMotion')
slowMotionButton.addEventListener('click', debugSkulpt)
const continueButton = document.getElementById('continue')
continueButton.addEventListener('click', debugSkulpt)

function runCode () {
  startStopButton.removeEventListener('click', runCode, false)
  startStopButton.style.backgroundColor = '#880000'
  startStopButton.textContent = 'Stop'
  startStopButton.addEventListener('click', stopCode, false)
  Sk.PyAngelo.console.innerHTML = ''
  const debugging = document.getElementById('debug').checked
  runSkulpt(aceEditor.getCode(session), debugging, stopCode)
}

function stopCode () {
  stopSkulpt()
  startStopButton.removeEventListener('click', stopCode, false)
  startStopButton.style.backgroundColor = '#008800'
  startStopButton.textContent = 'Start'
  startStopButton.addEventListener('click', runCode, false)
}

const onresize = (domElem, callback) => {
  const resizeObserver = new ResizeObserver(() => callback())
  resizeObserver.observe(domElem)
}

onresize(editorWindow, function () {
  aceEditor.resize()
})

aceEditor.addSession('')
aceEditor.setSession(session)

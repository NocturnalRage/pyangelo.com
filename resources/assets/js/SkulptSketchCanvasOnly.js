import { runSkulpt } from './SkulptSetup'
import { Editor } from './EditorSetup'
const Sk = require('skulpt')

const editorWindow = document.getElementById('editor')
const crsfToken = editorWindow.getAttribute('data-crsf-token')
const sketchId = editorWindow.getAttribute('data-sketch-id')
const isReadOnly = (editorWindow.getAttribute('data-read-only') === '1')
const fileTabs = document.getElementById('fileTabs')

const aceEditor = new Editor(sketchId, crsfToken, Sk, fileTabs, isReadOnly)
Sk.PyAngelo.aceEditor = aceEditor
aceEditor.loadCode()

setTimeout(runCode, 10)

function runCode () {
  if (aceEditor.editSessions[0] === undefined) {
    setTimeout(runCode, 10)
    return
  }
  const debugging = false
  runSkulpt(aceEditor.getCode(0), debugging, () => {})
}

const consoleWrapper = document.getElementById('consoleWrapper')
consoleWrapper.style.display = 'none'
const editorWrapper = document.getElementById('editorWrapper')
editorWrapper.style.display = 'none'
const pyEditorFiles = document.getElementById('editorFiles')
pyEditorFiles.style.display = 'none'
const buttonsWrapper = document.getElementById('buttonsWrapper')
buttonsWrapper.style.display = 'none'

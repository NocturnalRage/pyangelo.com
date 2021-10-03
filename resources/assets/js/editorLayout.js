import Split from 'split-grid'

const editorWrapper = document.getElementById('editorWrapper')
const editorWindow = document.getElementById('editor')
const editorGutter = document.getElementById('editorGutter')
const editorLayout = editorWindow.getAttribute('data-layout')
const crsfToken = editorWindow.getAttribute('data-crsf-token')
const sketchId = editorWindow.getAttribute('data-sketch-id')
const isOwner = (editorWindow.getAttribute('data-read-only') === '0')
const rowsRadioButton = document.getElementById('rows')
rowsRadioButton.addEventListener('click', updateSplit)

let split = setUpSplit(editorLayout)

function setUpSplit (editorLayout) {
  let options
  if (editorLayout === 'cols') {
    options = {
      columnGutters: [{
        track: 1,
        element: document.querySelector('.gutter-col-1')
      }]
    }
  } else {
    options = {
      rowGutters: [{
        track: 1,
        element: document.querySelector('.gutter-row-1')
      }]
    }
  }
  return Split(options)
}

function updateSplit (event) {
  let findLayout
  if (event.target.checked) {
    findLayout = 'rows'
    split.destroy()
    editorWrapper.classList.remove('grid-cols')
    editorWrapper.classList.add('grid-rows')
    editorGutter.classList.remove('gutter-col-1')
    editorGutter.classList.add('gutter-row-1')
    editorWrapper.style.gridTemplateColumns = '1fr'
    editorWrapper.style.gridTemplateRows = '1fr 5px 1fr'
  } else {
    findLayout = 'cols'
    split.destroy()
    editorWrapper.classList.remove('grid-rows')
    editorWrapper.classList.add('grid-cols')
    editorGutter.classList.remove('gutter-row-1')
    editorGutter.classList.add('gutter-col-1')
    editorWrapper.style.gridTemplateRows = '1fr'
    editorWrapper.style.gridTemplateColumns = '1fr 5px 1fr'
  }
  split = setUpSplit(findLayout)
  if (isOwner && !inPlayground()) {
    saveLayout()
  }
}

function saveLayout () {
  const layout = rowsRadioButton.checked ? 'rows' : 'cols'
  const data = 'layout=' + layout + '&crsfToken=' + encodeURIComponent(crsfToken)
  const options = {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: data
  }
  fetch('/sketch/' + sketchId + '/updateLayout', options)
    .then(response => response.json())
    .catch((error) => { console.error(error) })
}

function inPlayground () {
  if (sketchId === '0') {
    return true
  }
  return false
}

import { notify } from './pyangelo-notify'

const showNewCollectionLink = document.getElementById('newCollection')
const newCollectionSubmitButton = document.getElementById('newCollectionSubmit')
const newCollectionCancelButton = document.getElementById('newCollectionCancel')
const collectionSelects = document.getElementsByClassName('collectionSelect')

Array.from(collectionSelects).forEach(function (collectionSelect) {
  collectionSelect.addEventListener('change', setCollectionForSketch)
})

showNewCollectionLink.addEventListener('click', showNewCollection)
newCollectionSubmitButton.addEventListener('click', submitNewCollection)
newCollectionCancelButton.addEventListener('click', cancelNewCollection)

function setCollectionForSketch (event) {
  event.preventDefault()
  const collectionId = event.target.value
  const sketchId = event.target.getAttribute('data-sketch-id')
  const crsfToken = event.target.getAttribute('data-crsf-token')
  addSketchToCollection(sketchId, collectionId, crsfToken)
}

function showNewCollection (event) {
  event.preventDefault()
  document.getElementById('newCollection').style.display = 'none'
  document.getElementById('new-collection-form').style.display = 'block'
}

function submitNewCollection (event) {
  event.preventDefault()
  const collectionTitle = document.getElementById('collectionTitle').value
  if (!collectionTitle || /^\s*$/.test(collectionTitle)) {
    alert('You must provide a name for the collection!')
    return
  }
  document.getElementById('new-collection-form').submit()
}

function cancelNewCollection (event) {
  event.preventDefault()
  document.getElementById('new-collection-form').style.display = 'none'
  document.getElementById('newCollection').style.display = 'block'
}

function addSketchToCollection (sketchId, collectionId, crsfToken) {
  const data = 'sketchId=' + encodeURIComponent(sketchId) + '&collectionId=' + encodeURIComponent(collectionId) + '&crsfToken=' + encodeURIComponent(crsfToken)
  const options = {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: data
  }
  fetch('/collection/add-sketch', options)
    .then(response => response.json())
    .then(data => {
      if (data.status === 'added') {
        notify('Sketch added to collection', 'success')
      } else if (data.status === 'removed') {
        notify('Sketch removed from collection', 'success')
      } else {
        notify('Sketch added to collection', 'error')
        throw new Error(data.message)
      }
    })
    .catch(error => { console.error(error) })
}

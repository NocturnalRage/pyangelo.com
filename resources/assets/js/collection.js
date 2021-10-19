import { notify } from './pyangelo-notify'

const showNewCollectionLink = document.getElementById('newCollection')
const newCollectionSubmitButton = document.getElementById('newCollectionSubmit')
const newCollectionCancelButton = document.getElementById('newCollectionCancel')
const collectionSelects = document.getElementsByClassName('collectionSelect')
const showRenameLink = document.getElementById('rename')
const renameSubmitButton = document.getElementById('renameSubmit')
const renameCancelButton = document.getElementById('renameCancel')

Array.from(collectionSelects).forEach(function (collectionSelect) {
  collectionSelect.addEventListener('change', setCollectionForSketch)
})

showNewCollectionLink.addEventListener('click', showNewCollectionForm)
newCollectionSubmitButton.addEventListener('click', submitNewCollection)
newCollectionCancelButton.addEventListener('click', cancelNewCollection)
showRenameLink.addEventListener('click', showRename)
renameSubmitButton.addEventListener('click', submitRename)
renameCancelButton.addEventListener('click', cancelRename)

function setCollectionForSketch (event) {
  event.preventDefault()
  const collectionId = event.target.value
  const sketchId = event.target.getAttribute('data-sketch-id')
  const crsfToken = event.target.getAttribute('data-crsf-token')
  addSketchToCollection(sketchId, collectionId, crsfToken)
}

function showNewCollectionForm (event) {
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

function showRename (event) {
  event.preventDefault()
  document.getElementById('rename-form').style.display = 'block'
  document.getElementById('titleWithEdit').style.display = 'none'
}

function submitRename (event) {
  event.preventDefault()
  const newTitle = document.getElementById('newTitle').value
  if (!newTitle || /^\s*$/.test(newTitle)) {
    alert('You must provide a name for your collection!')
    return
  }
  const titleWithEdit = document.getElementById('titleWithEdit')
  const crsfToken = titleWithEdit.getAttribute('data-crsf-token')
  const collectionId = titleWithEdit.getAttribute('data-collection-id')
  const data = 'newTitle=' + newTitle + '&collctionId=' + encodeURIComponent(collectionId) + '&crsfToken=' + encodeURIComponent(crsfToken)
  const options = {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: data
  }
  fetch('/collection/' + collectionId + '/rename', options)
    .then(response => response.json())
    .then(data => {
      if (data.status !== 'success') {
        throw new Error(data.message)
      }
      updateTitle(data)
      notify('Collection name updated.', 'success')
    })
    .catch((error) => {
      console.error(error)
      notify('Error: collection name not updated.', 'error')
    })
}

function updateTitle (data) {
  document.getElementById('rename-form').style.display = 'none'
  document.getElementById('title').innerHTML = data.title
  document.getElementById('collection' + data.collectionId).innerHTML = data.title
  document.getElementById('titleWithEdit').style.display = 'block'
}

function cancelRename (event) {
  event.preventDefault()
  document.getElementById('rename-form').style.display = 'none'
  document.getElementById('titleWithEdit').style.display = 'block'
}

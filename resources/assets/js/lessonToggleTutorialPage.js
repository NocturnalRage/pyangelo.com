$(document).ready(function () {
  const lessonCompleteToggle = (function () {
    const setCompleteInTable = function (lessonId) {
      $('.btn' + lessonId).toggleClass('btn-default').toggleClass('btn-success')
    }
    const unsetCompleteInTable = function (lessonId) {
      $('.btn' + lessonId).toggleClass('btn-success').toggleClass('btn-default')
    }
    const updatePercentComplete = function (percentComplete) {
      $('#percent-complete').html(percentComplete)
    }

    const toggleCompleteVideo = function (lessonId) {
      $.ajax({
        type: 'POST',
        url: '/toggle-lesson-completed',
        data: { lessonId: lessonId, action: 'toggle' }
      })
        .done(function (data) {
          $.notify(data.message, { className: data.status, position: 'right-bottom' })
          if (data.status === 'info') {
            unsetCompleteInTable(lessonId)
          } else {
            setCompleteInTable(lessonId)
          }
          updatePercentComplete(data.percentComplete)
        })
        .fail(function () {
          $.notify('There was an issue and we could not record the completion of your lesson.', { className: 'error', position: 'right-bottom' })
        })
        .always(function () {
          // Do something only if requried
        })
    }

    const bindFunctions = function () {
      $('.toggleComplete').on('click', toggleButtonClick)
    }

    const init = function () {
      bindFunctions()
    }

    const toggleButtonClick = function (e) {
      e.preventDefault()
      const lessonId = $(this).data('lesson-id')
      toggleCompleteVideo(lessonId)
    }
    return {
      init: init
    }
  })()

  lessonCompleteToggle.init()
})

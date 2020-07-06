$( document ).ready(function() {
  var lessonCompleteToggle = (function() {

    var setCompleteInTable = function(lesson_id) {
      $('.btn' + lesson_id).toggleClass('btn-default').toggleClass('btn-success');
    }
    var unsetCompleteInTable = function(lesson_id) {
      $('.btn' + lesson_id).toggleClass('btn-success').toggleClass('btn-default');
    }
    var updatePercentComplete = function(percentComplete) {
      $('#percent-complete').html(percentComplete);
    }

    var toggleCompleteVideo = function(lesson_id) {
      $.ajax({
        type: 'POST',
        url: '/toggle-lesson-completed',
        data: { lessonId: lesson_id, action: 'toggle' }
      })
      .done(function(data) {
        $.notify(data.message, { className: data.status, position:"right-bottom" });
        if (data.status == "info") {
          unsetCompleteInTable(lesson_id);
        }
        else {
          setCompleteInTable(lesson_id);
        }
        updatePercentComplete(data.percentComplete);
      })
      .fail(function() {
        $.notify('There was an issue and we could not record the completion of your lesson.', { className: 'error', position:"right-bottom" });
      })
      .always(function() {
          // Do something only if requried
      });
    }

    var bindFunctions = function() {
      $(".toggleComplete").on("click", toggleButtonClick)
    };

    var init = function() {
      bindFunctions();
    };

    var toggleButtonClick = function(e) {
      e.preventDefault();
      lesson_id = jQuery(this).data('lesson-id');
      toggleCompleteVideo(lesson_id);
    };
    return {
      init: init
    };
  })();

  lessonCompleteToggle.init();
});

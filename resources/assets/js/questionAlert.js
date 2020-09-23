$( document ).ready(function() {
  var questionAlert = (function() {
    var setAlert = function() {
      $('#alertStatus').removeClass('btn-primary').addClass('btn-info', {duration: 1000});
      $('#alertStatus').html('<i class="fa fa-bookmark aria-hidden="true"></i> Stop notifications');
    };

    var unsetAlert = function() {
      $('#alertStatus').removeClass('btn-info').addClass('btn-primary', {duration: 1000});
      $('#alertStatus').html('<i class="fa fa-bookmark aria-hidden="true"></i> Notify me of updates');
    };

    var toggleQuestionAlert = function(question_id, crsf_token) {
      $.ajax({
        type: 'POST',
        url: '/toggle-question-alert',
        data: { questionId: question_id, crsfToken: crsf_token }
      })
      .done(function(data) {
        $.notify(data.message, { className: data.status, position:"right-bottom" });
        if (data.status == "info") {
          unsetAlert();
        }
        else if (data.status == "success") {
          setAlert();
        }
      })
      .fail(function() {
        $.notify('We could not record your notification advice.', { className: 'error', position:"right-bottom" });
      })
      .always(function() {
          // Do something only if requried
      });
    };

    var bindFunctions = function() {
      $("#alertStatus").on("click", toggleAlert);
    };

    var toggleAlert = function(e) {
      e.preventDefault();
      question_id = jQuery(this).data('question-id');
      crsf_token = jQuery(this).data('crsf-token');
      toggleQuestionAlert(question_id, crsf_token);
    };

    var init = function() {
      bindFunctions();
    };

    return {
      init: init
    };
  })();

  questionAlert.init();
});

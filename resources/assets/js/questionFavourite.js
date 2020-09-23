$( document ).ready(function() {
  var questionFavourite = (function() {
    var setFavourite = function() {
      $('#favouriteStatus').removeClass('btn-primary').addClass('btn-primary', {duration: 1000});
      $('#favouriteStatus').html('<i class="fa fa-star aria-hidden="true"></i> Favourites');
    };

    var unsetFavourite = function() {
      $('#favouriteStatus').removeClass('btn-primary', {duration: 1000});
      $('#favouriteStatus').html('<i class="fa fa-star aria-hidden="true"></i> Add to favourites');
    };

    var toggleQuestionFavourite = function(question_id, crsf_token) {
      $.ajax({
        type: 'POST',
        url: '/toggle-question-favourite',
        data: { questionId: question_id, crsfToken: crsf_token }
      })
      .done(function(data) {
        $.notify(data.message, { className: data.status, position:"right-bottom" });
        if (data.status == "info") {
          unsetFavourite();
        }
        else if (data.status == "success") {
          setFavourite();
        }
      })
      .fail(function() {
        $.notify('We could not record your favourite.', { className: 'error', position:"right-bottom" });
      })
      .always(function() {
          // Do something only if requried
      });
    };

    var bindFunctions = function() {
      $("#favouriteStatus").on("click", toggleFavourite);
    };

    var toggleFavourite = function(e) {
      e.preventDefault();
      question_id = jQuery(this).data('question-id');
      crsf_token = jQuery(this).data('crsf-token');
      toggleQuestionFavourite(question_id, crsf_token);
    };

    var init = function() {
      bindFunctions();
    };

    return {
      init: init
    };
  })();

  questionFavourite.init();
});

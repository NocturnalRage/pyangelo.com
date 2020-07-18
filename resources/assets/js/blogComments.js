$( document ).ready(function() {
  var blogComments = (function() {

    var commentForm = $('#commentForm');

    var processComment = function(e) {
      e.preventDefault();
      $('#waiting').remove();
      $('#submitComment').prop("disabled", true);
      $('<div id="waiting"><img src="/images/icons/ajax-loader.gif" /> adding your comment...</div>').insertBefore( "#add-comment");
      $('#add-comment').slideUp();
      tinyMCE.triggerSave();
      $.ajax({
        type: 'POST',
        url: '/blog/comment',
        data: commentForm.serialize()
      })
      .done(function(data) {
        if (data.status == "success") {
          $('#waiting').slideUp();
          if ($('#comments').length == 0) {
            $('<div id="comments" class="add-bottom"></div>').insertBefore("#add-comment");
          }
          $('#comments').append(data.commentHtml);
          tinyMCE.get('blogComment').setContent('');
          $('#submitComment').prop("disabled", false);
          $('#add-comment').slideDown();
        }
        else if (data.status == "error") {
          $('#waiting').html(data.message);
          $('#submitComment').prop("disabled", false);
          $('#add-comment').slideDown();
        }
      })
      .fail(function() {
        $('#waiting').html('There was an error and we could not add your comment.');
        $('#submitComment').prop("disabled", false);
        $('#add-comment').slideDown();
        $.notify('There was an error and we could not add your comment.', { className: 'error', position:"right-bottom" });
      })
      .always(function() {
          // Do something only if requried
      });
    }

    var bindFunctions = function() {
      $("#submitComment").on("click", processComment);
      $("#showMoreComments").on("click", displayComments);
    };

    var addSubmitButton = function() {
      var buttonHtml = '<button id="submitComment" type="submit" class="btn btn-success"><i class="fa fa-comment-o" aria-hidden="true"></i> Add my comment</button>';
      $('#submitButtonDiv').append(buttonHtml);
    };

    var hideComments = function() {
      $('.hideComment').hide();
    };

    var displayComments = function(e) {
      e.preventDefault();
      $('.hideComment').slideDown();
      $('#showMoreCommentsDiv').slideUp();
    };

    var init = function() {
      addSubmitButton();
      hideComments();
      bindFunctions();
    };

    return {
      init: init
    };
  })();

  blogComments.init();
});

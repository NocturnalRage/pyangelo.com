$( document ).ready(function() {
  var lessonStatus = (function() {
    var videoType;
    var video;
    var whatNextPanel = $('#whatNextPanel');
    var loadingVideoPanel = $('#loadingVideoPanel');
    var videoTag = $('#pyangelo-lesson');
    var currentLessonId = videoTag.data('lesson-id');
    var currentTutorialId = videoTag.data('tutorial-id');
    var currentDisplayOrder = videoTag.data('display-order');

    var setComplete = function(lesson_id) {
      $('.btn' + lesson_id).removeClass('btn-default').addClass('btn-success', {duration: 1000});
      if (currentLessonId == lesson_id) {
        $('#completeStatus').html('<i class="fa fa-check aria-hidden="true"></i> Complete');
      }
    };

    var unsetComplete = function(lesson_id) {
      $('.btn' + lesson_id).removeClass('btn-success').addClass('btn-default', {duration: 1000});
      if (currentLessonId == lesson_id) {
        $('#completeStatus').html('<i class="fa fa-check aria-hidden="true"></i> Incomplete');
      }
    };

    var updatePercentComplete = function(percentComplete) {
      $('#percent-complete').html(percentComplete);
    };

    var setFavourite = function() {
      $('#favouriteStatus').removeClass('btn-default').addClass('btn-primary', {duration: 1000});
      $('#favouriteStatus').html('<i class="fa fa-star aria-hidden="true"></i> Favourite');
    };

    var unsetFavourite = function() {
      $('#favouriteStatus').removeClass('btn-primary').addClass('btn-default', {duration: 1000});
      $('#favouriteStatus').html('<i class="fa fa-star aria-hidden="true"></i> Add to Favourites');
    };

    var toggleLessonCompleted = function(lesson_id, action) {
      $.ajax({
        type: 'POST',
        url: '/toggle-lesson-completed',
        data: { lessonId: lesson_id, action: action }
      })
      .done(function(data) {
        $.notify(data.message, { className: data.status, position:"right-bottom" });
        if (data.status == "info") {
          unsetComplete(lesson_id);
        }
        else {
          setComplete(lesson_id);
        }
        updatePercentComplete(data.percentComplete);
      })
      .fail(function() {
        $.notify('We could not record the completion of your lesson.', { className: 'error', position:"right-bottom" });
      })
      .always(function() {
          // Do something only if requried
      });
    };

    var toggleLessonFavourited = function(lesson_id) {
      $.ajax({
        type: 'POST',
        url: '/toggle-lesson-favourited',
        data: { lessonId: lesson_id }
      })
      .done(function(data) {
        $.notify(data.message, { className: data.status, position:"right-bottom" });
        if (data.status == "info") {
          unsetFavourite();
        }
        else {
          setFavourite();
        }
      })
      .fail(function() {
        $.notify('We could not add this to your favourites.', { className: 'error', position:"right-bottom" });
      })
      .always(function() {
          // Do something only if requried
      });
    };

    var setAlert = function() {
      $('#alertStatus').removeClass('btn-primary').addClass('btn-info', {duration: 1000});
      $('#alertStatus').html('<i class="fa fa-bookmark aria-hidden="true"></i> Stop notifications');
    };

    var unsetAlert = function() {
      $('#alertStatus').removeClass('btn-info').addClass('btn-primary', {duration: 1000});
      $('#alertStatus').html('<i class="fa fa-bookmark aria-hidden="true"></i> Notify me of updates');
    };

    var toggleBlogAlert = function(lesson_id, crsf_token) {
      $.ajax({
        type: 'POST',
        url: '/toggle-lesson-alert',
        data: { lessonId: lesson_id, crsfToken: crsf_token }
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
      $(".toggleComplete").on("click", toggleCompleted);
      $("#favouriteStatus").on("click", toggleFavourited);
      $("#replayVideo").on("click", replayVideo);
      $("#alertStatus").on("click", toggleAlert);
    };

    var toggleCompleted = function(e) {
      e.preventDefault();
      lesson_id = jQuery(this).data('lesson-id');
      toggleLessonCompleted(lesson_id, 'toggle');
    };

    var toggleFavourited = function(e) {
      e.preventDefault();
      lesson_id = jQuery(this).data('lesson-id');
      toggleLessonFavourited(lesson_id);
    };

    var replayVideo = function(e) {
      e.preventDefault();
      whatNextPanel.slideUp();
      videoTag.slideDown();
      if (videoType == 'YouTube') {
        video.playVideo();
      }
      else {
        video.play();
      }
    };

    var toggleAlert = function(e) {
      e.preventDefault();
      lesson_id = jQuery(this).data('lesson-id');
      crsf_token = jQuery(this).data('crsf-token');
      toggleBlogAlert(lesson_id, crsf_token);
    };

    var getSignedUrl = function(lesson_id) {
      $.ajax({
        type: 'POST',
        url: '/get-signed-url',
        data: { lessonId: lesson_id }
      })
      .done(function(data) {
        if (data.status == "success") {
          if (data.youtubeUrl) {
            videoType = 'YouTube';
            loadYoutubeVideo();
          }
          else {
            videoType = 'S3';
            video = videojs('pyangelo-lesson', {
              playbackRates: [.25, .5, 1, 1.5, 2],
              fluid: true,
              plugins: {
                hotkeys: {
                  seekStep: 10,
                  enableNumbers: false
                }
              },
            });
            setVideoSrc(data.signedUrl);

            // disable browser context menu
            video.on('contextmenu', function(e) {
              e.preventDefault();
            });

            video.on('ended', function() {
              processVideoCompleted();
            });

            loadingVideoPanel.slideUp();
            videoTag = $('#pyangelo-lesson');
            videoTag.slideDown();
          }
        }
        else {
          $.notify('We could not load the video. Please try again.', { className: 'error', position:"right-bottom" });
        }
      })
      .fail(function() {
        $.notify('We could not load the video. Please try again.', { className: 'error', position:"right-bottom" });
      })
      .always(function() {
          // Do something only if requried
      });
    };

    var processVideoCompleted = function() {
      toggleLessonCompleted(currentLessonId, 'complete');
      getNextVideo(currentLessonId, currentTutorialId, currentDisplayOrder);
    }

    var loadYoutubeVideo = function() {
      var tag = document.createElement('script');
      tag.id = 'iframe-demo';
      tag.src = 'https://www.youtube.com/iframe_api';
      var firstScriptTag = document.getElementsByTagName('script')[0];
      firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
      console.log('iframe_api added');
    };

    window.onYouTubeIframeAPIReady = function() {
      console.log('YouTubeIframeAPIReady');
      video = new YT.Player('pyangelo-video', {
          events: {
            'onReady': onPlayerReady,
            'onStateChange': onPlayerStateChange
          }
      });
    }

    var onPlayerReady = function(event) {
      console.log('Player ready');
      loadingVideoPanel.slideUp();
      videoTag.slideDown();
    }

    var onPlayerStateChange = function(event) {
      if (event.data == YT.PlayerState.ENDED) {
        processVideoCompleted();
      }
    }

    var setVideoSrc = function(signedUrl, youtubeUrl) {
      video.src({ type: "video/mp4", src: signedUrl });
    };

    var getNextVideo = function(lessonId, tutorialId, displayOrder) {
      $.ajax({
        type: 'GET',
        url: '/get-next-video',
        data: { tutorialId: tutorialId, displayOrder: displayOrder }
      })
      .done(function(data) {
        if (data.status == "success") {
          showNextVideoPanel(
            data.lessonTitle,
            data.tutorialSlug,
            data.lessonSlug
          );
        }
        else if (data.status == "completed") {
          showCompletedTutorialPanel();
        }
        else {
          $.notify('We could not determine the next video.', { className: 'info', position:"right-bottom" });
        }
      })
      .fail(function() {
        $.notify('We could not determine the next video.', { className: 'error', position:"right-bottom" });
      })
      .always(function() {
          // Do something only if requried
      });
    };

    var showNextVideoPanel = function(lessonTitle, tutorialSlug, lessonSlug) {
      videoTag.slideUp();
      $('#nextLessonButton').html('<i class="fa fa-arrow-right aria-hidden="true"></i> ' + lessonTitle);
      $('#nextLessonButton').attr('href', '/tutorials/' + tutorialSlug + '/' + lessonSlug);
      whatNextPanel.slideDown();
    };

    var showCompletedTutorialPanel = function() {
      videoTag.slideUp();
      $('#nextLessonButton').hide();
      whatNextPanel.slideDown();
    };

    var init = function() {
      getSignedUrl(currentLessonId);
      bindFunctions();
    };

    return {
      init: init
    };
  })();

  lessonStatus.init();
});

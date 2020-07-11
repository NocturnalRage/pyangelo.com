$( document ).ready(function() {
  var notifications = (function() {
    crsf_token = jQuery('#notifications-table').data('crsf-token');
    selection = jQuery('#notifications-table').data('selection');

    var markAsReadAjax = function(notification_id, crsf_token) {
      $.ajax({
        type: 'POST',
        url: '/notification-read',
        data: { notificationId: notification_id, crsfToken: crsf_token }
      })
      .done(function(data) {
        $.notify(data.message, { className: data.status, position:"right-bottom" });
        if (data.status == "success") {
          updateReadRow(notification_id);
        }
      })
      .fail(function() {
        $.notify('We could not mark your notification as read.', { className: 'error', position:"right-bottom" });
      })
      .always(function() {
          // Do something only if requried
      });
    };

    var unsubscribeThreadAjax = function(notification_type_id, notification_type, crsf_token) {
      $.ajax({
        type: 'POST',
        url: '/unsubscribe-thread',
        data: { notificationTypeId: notification_type_id, notificationType: notification_type, crsfToken: crsf_token }
      })
      .done(function(data) {
        $.notify(data.message, { className: data.status, position:"right-bottom" });
      })
      .fail(function() {
        $.notify('We could not unsubscribe you.', { className: 'error', position:"right-bottom" });
      })
      .always(function() {
          // Do something only if requried
      });
    };

    var markAllAsReadAjax = function(crsf_token) {
      $.ajax({
        type: 'POST',
        url: '/notification-all-read',
        data: { crsfToken: crsf_token }
      })
      .done(function(data) {
        $.notify(data.message, { className: data.status, position:"right-bottom" });
        if (data.status == "success") {
          if (selection == 'all') {
            jQuery('.unread').removeClass('unread').addClass('read').addClass('warning');
            jQuery('.markAsRead').fadeOut(500);
          }
          else {
            jQuery('.unread').fadeOut(500);
          }
          jQuery('#notification-badge').text(0);
        }
      })
      .fail(function() {
        $.notify('We could not mark your notifications as read.', { className: 'error', position:"right-bottom" });
      })
      .always(function() {
          // Do something only if requried
      });
    };

    var updateReadRow = function(notification_id) {
      if (selection == 'all') {
        jQuery('#notification'+notification_id).removeClass('unread').addClass('read').addClass('warning');
        jQuery('#markAsRead'+notification_id).fadeOut(500);
      }
      else {
        jQuery('#notification'+notification_id).fadeOut(500);
      }
      jQuery('#notification-badge').text(
        parseInt(jQuery("#notification-badge").text()) - 1
      );
    }

    var bindFunctions = function() {
      $(".markAsRead").on("click", markAsReadPreventDefault);
      $(".notification-link").on("click", markAsRead);
      $(".thread-unsubscribe").on("click", unsubscribeFromThread);
      $("#markAllAsRead").on("click", markAllAsRead);
    };

    var markAsReadPreventDefault = function(e) {
      e.preventDefault();
      notification_id = jQuery(this).data('notification-id');
      markAsReadAjax(notification_id, crsf_token);
    };

    var markAsRead = function(e) {
      notification_id = jQuery(this).data('notification-id');
      markAsReadAjax(notification_id, crsf_token);
    };

    var unsubscribeFromThread = function(e) {
      e.preventDefault();
      notification_type_id = jQuery(this).data('notification-type-id');
      notification_type = jQuery(this).data('notification-type');
      
      unsubscribeThreadAjax(notification_type_id, notification_type, crsf_token);
    };

    var markAllAsRead = function(e) {
      e.preventDefault();
      markAllAsReadAjax(crsf_token);
    };

    var init = function() {
      bindFunctions();
    };

    return {
      init: init
    };
  })();

  notifications.init();
});

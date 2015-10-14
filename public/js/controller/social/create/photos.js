(function ($) {

  var $bar = $('.bar');
  var $file_name = $('#filename');
  var $load_div = $('.progress-div');
  var $progress = $('.progress');

  var $form = $('#post-link-form');

  var $loading = $('<div>').addClass('loading');

  var $container = $('#ajax-container');

  /**
   * Show it then post successfully created
   *
   * @type {*|HTMLElement}
   */
  var $successfully_message = $('#successfully-posted');

  $(".inputFile").fileupload({
    acceptFileTypes: /^image\/(gif|jpeg|jpg|png)$/,
    maxFileSize: 10000000,
    dataType: 'json',
    done: function (e, data) {
      $progress.hide();
      $bar.css('width', '0');

      var result = data.result[0];

      if (undefined !== result.error && result.error.length) {
        $('.file').show();
        $file_name.empty();
        $load_div.parent().find('label').append('<span class="message-error">' + result.error + '</span>');
        return;
      }
      var size = data.files[0].size / 1000;
      var remove_link = '<a id="remove-image" data-target="' + data.files[0].name + '" href="#">Remove</a>';
      $file_name.html($file_name.html() + ' (' + size + ' kB) ' + remove_link);

      $form.find('input[name=image_name]').val(data.files[0].name).trigger('change');
      $form.find('.load.progress-div').show();
      $('.file').hide();
      $load_div.parent().find('.message-error').remove();
    },
    start: function () {
      $('.file').hide();
    },
    progress: function (e, data) {
      $load_div.show();
      $progress.show();
      var progress = parseInt(data.loaded / data.total * 100, 10);
      $bar.css('width', progress + '%');
    },
    fail: function (e, data) {
      $('.file').show();
      $load_div.parent().find('label').append('<span class="message-error">File type not supported. Please try again with a different image</span>');
    },
    add: function (e, data) {
      $('.message-error').remove();
      $(".inputFile").fileupload("send", {files: data.files});
      $file_name.html(data.files[0].name);
    },
    autoUpload: true,
    url: 'upload_images'
  });

  $form.on('click', '#remove-image', function () {
    var $self = $(this);
    $.ajax({
      url: g_settings.base_url + 'social/create/upload_images?file=' + $self.data('target'),
      type: 'DELETE',
      success: function (success) {
        if (success) {
          $('.file').show();
          $file_name.empty();
          $form.find('input[name=image_name]').val('').trigger('change');
        }
      }
    });
    return false;
  });

  $form.on('submit', function () {
    var $self = $(this);
    $container.prepend($loading);
    $('.message-error').remove();
    $successfully_message.hide();

    $.ajax({
      url: $self.attr('action'),
      data: $self.serialize(),
      type: 'POST',
      dataType: 'JSON',
      success: function (response) {
        if (response.success) {
          $loading.remove();
          $successfully_message.show();
          $('html, body').animate({scrollTop: $successfully_message.offset().top - 10}, 'slow');
          $self[0].reset();
          $self.find('span.checkbox, span.radio').css('background-position', '0 0');
          $self.find('#schedule-settings').hide();
          $self.find('.file').show();
          $self.find('#filename').empty();
          $self.find('.char-counter').empty();
          $self.find('input').iCheck('update');
        } else {
          for (var error in response.errors) {
            $container.find('input[name="' + error + '"]').parents('.control-group').find('label').append(response.errors[error]);
            $container.find('textarea[name="' + error + '"]').parents('.control-group').find('label').append(response.errors[error]);
          }
          $loading.remove();
        }
      }
    });
    return false;
  });


})(jQuery);
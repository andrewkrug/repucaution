(function ($) {
$(document).ready(function(){
    /**
     * 'Post a link' radionbutton
     *
     * @type {*|HTMLElement}
     */
    var $post_a_link_button = $('#radio_1');

    /**
     * 'Suggested links' radionbutton
     *
     * @type {*|HTMLElement}
     */
    var $suggested_links_button = $('#radio_2');

  /**
   * 'Custom RSS feed' radionbutton
   *
   * @type {*|HTMLElement}
   */
  var $rss_feed_button = $('#radio_3');

  /**
   * Loading div
   *
   * @type {*}
   */
  var $loading = $('<div>').addClass('loading');

  /**
   * Contains 'post a link' form / Suggested RSS form / Custom RSS form
   *
   * @type {*|HTMLElement}
   */
  var $container = $('#ajax-container');

  /**
   * Used for RSS links -- fill then user click on post radiobutton
   * Contains link for selected post
   * Then we load 'post a link' form - insert it to 'url' field
   *
   * @type {String}
   */
  var selected_link = '';

  /**
   * See 'selected_link' for more comments
   * Then we load 'post a link' form - insert it to 'description' textarea
   *
   * @type {String}
   */
  var selected_description = '';

    /**
     * Show it then post successfully created
     *
     * @type {*|HTMLElement}
     */
    var $successfully_message = $('#successfully-posted');

    /**
     * Show indicator of progress of uploading of image, video
     *
     * @type {*|HTMLElement}
     */
    var $bar = $('.bar');
  
    /**
     * Container for file is uploading
     *
     * @type {*|HTMLElement}
     */
    var $file_name = $('#filename');
    
    /**
     * Container for preview file is uploading
     *
     * @type {*|HTMLElement}
     */
    var $load_div = $('.load.preview');
    
    /**
     * Indicator of progress of uploading file
     *
     * @type {*|HTMLElement}
     */
    var $progress = $('.progress');
    
    
    /**
     * Form for post to socials
     *
     * @type {*|HTMLElement}
     */
    var $form = $('#post-update-form');
    
    /**
     * Container of feed of rss
     *
     * @type {*|HTMLElement}
     */
    var $fcontainer = $('div.feed-container');

  /**
   * Custom RSS feed block - click on one of the user RSS titles
   * Load selected RSS feed into section right
   */
  $container.on('change', 'select[name="feed"]', function () {
    var $self = $(this);
    
    $fcontainer.html($loading);
    $.ajax({
      url: g_settings.base_url + 'social/create/post_rss',
      type: 'POST',
      dataType:'json',
      data: 'feed=' + $self.val(),
      success: function (response) {
            if (response.success) {
                $loading.replaceWith(response.html);
                //update selectik items
                $(".select_block").each(function (indx) {
                  var self = $(this);
                  $(this).ddslick({
                    width: self.data('width') || 174,
                    height: self.data('height') || null
                  })
                });

                $container.find('input').iCheck({
                  checkboxClass: 'icheckbox_minimal-grey',
                  radioClass: 'iradio_minimal-grey',
                  increaseArea: '20%' // optional
                });
           
            }         
      }
    });
    return false;
  });
  /**
   * Load forms into bottom of the page (post a ling / RSS)
   * Get html from controller
   *
   * @param action
   */
  function load_blocks_html(action) {
    $.ajax({
      url: g_settings.base_url + 'social/create/' + action,
      type: 'POST',
      success: function (html) {

        $container.html(html);

        rebind_vars($container);
       
        //update selectik items
        $(".select_block").each(function (indx) {
          var self = $(this);
          $(this).ddslick({
            width: self.data('width') || 174,
            height: self.data('height') || null
          })
        });

        $container.find('input').iCheck({
          checkboxClass: 'icheckbox_minimal-grey',
          radioClass: 'iradio_minimal-grey',
          increaseArea: '20%' // optional
        });


      },
      complete: function () {
        //if we load html after selecting RSS post
        // we need to insert selected data into inputs
        if ($container.find('input[name=url]').length) {
          $container.find('input[name=url]').val(selected_link);
          $container.find('textarea[name=description]').val(selected_description.trim());
           $('#attachment').hide();
        }

        

      }
    })
  }
  
  /**
   * Hide schedule date settings (date / time / am \ pm)
   */
  $('.container').on('ifChanged', '#immediate-type', function () {
    $('.container').find('#schedule-settings').hide();
  });

  /**
   * Show schedule date settings (date / time / am \ pm)
   */
  $('.container').on('ifChanged', '#schedule-type', function () {
    $(".datepicker").datepicker({
      changeMonth: true,
      changeYear: true,
      showOtherMonths: true,
      dateFormat: "yy-mm-dd"
    });
    $('.container').find('#schedule-settings').show();
  });
  
  /**
   * Strip HTML and PHP tags from a string
   *
   * @param str
   * @returns {*|XML|string|void}
   */
  function strip_tags(str) {
    return str.replace(/<\/?[^>]+>/gi, '');
  }
  
  
  
  /**
   * Click of some of posts in RSS feed
   * (click on 'radio' to make it checked)
   */
  $container.on('ifChecked', 'input[name=rss_feed_item]', function () {
    var $self = $(this);
    var $info_container = $self.parents('.post').find('.textBox');
    selected_link = $info_container.find('a').attr('href');
    selected_description = strip_tags($self.parent().parent().text());
  });
  
  /**
   * Checked RSS post data sended to 'post a link' form
   */
  $container.on('click', '#post-custom-rss-link', function () {
    load_blocks_html('get_post_a_link_html');
    return false;
  });
  
  /**
   * Send data to posting link
   */
    //Do we need this piece of code???
  $container.on('submit', '#post-update-form', function () {
    var $self = $(this);
    $container.prepend($loading);
    $successfully_message.hide();
    $('.message-error').remove();

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
          $self.find('span.checkBox, span.radio').css('background-position', '0 0');
          $self.find('#schedule-settings').hide();
          if (!$('[name="post_id"]').length) {
              $self.find('.file').show();
          }
          $self.find('#filename').empty();
          $self.find('.char-counter').empty();
          $self.find('input').iCheck('update');
          //window.location.href = g_settings.base_url + 'social/create/post_rss';
        } else {
          for (var error in response.errors) {
            $container.find('input[name="' + error + '"]').parents('.control-group').find('label').append(response.errors[error]);
            $container.find('textarea[name="' + error + '"]').parents('.control-group').find('label').append(response.errors[error]);
            $container.find('label[error-container="' + error + '"]').append(response.errors[error]);
          }
          $loading.remove();
        }
      }

    });
    return false;
  });

    var options = {
        maxFileSize: 10000000,
        dataType: 'json',
        multiple:true,
        done: function (e, data) {
            $load_div.hide();
            $bar.css('width', '0');

            var result = data.result[0];

            if (undefined !== result.error && result.error.length) {
                $('.file').show();
                $file_name.empty();
                $load_div.parent().find('label').append('<span class="message-error">' + result.error + '</span>');
                return;
            }
            var size = data.files[0].size / 1000;
            var type = $(this).attr('id');
            var preview;
            if (!(type == 'videos')) {
                preview = '<div class="preview" >'+
                    '<img class="img-close" src="'+g_settings.base_url+'/public/images/im_prev_close.png" />'+
                    '<img class="img-preview" src="'+result.url+'" />'+
                    '</div>';
            } else {
                preview = '<div class="preview" >'+
                    '<img class="img-close" src="'+g_settings.base_url+'/public/images/im_prev_close.png" />'+
                    '<video class="img-preview">'+
                    '<source src="'+result.url+'"/>'+
                    '<span>'+data.files[0].name+'</span>'+

                    '</video></div>';
            }
            $(preview).insertBefore($load_div);

            $form.find('input[name=image_name]').val(data.files[0].name).trigger('change');
            $form.find('input[name=attachment_type]').val(type).trigger('change');
            //$form.find('.load.progress-div').show();
            if ($(this).attr('id')!='gallery') {
                $('.file').hide();
            }
            $load_div.parent().find('.message-error').remove();
        },
        start: function () {
            if ($(this).attr('id')!='gallery') {
                $('.file').hide();
            }
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

            if($(this).attr('id') == 'videos') {
                acceptFileTypes = /\.(wmv|avi|mpe?g|mp4|webm|3gpp|mpegps|flv)$/;
            }else{
                acceptFileTypes = /\.(gif|jpeg|jpg|png)$/;
            }
            if(!acceptFileTypes.test(data.files[0].name)){
                $('.file').show();
                $load_div.parent().find('label').append('<span class="message-error">Not allowed file type</span>');
                return false;
            } else {
                data.submit();
                $file_name.html(data.files[0].name);
            }

        },
        autoUpload: true,
        url: g_settings.base_url+'social/create/upload_images'
    };

    $(".inputFile").fileupload(options);

    $container.on('click', '.img-close', function () {
    var $self = $(this);
    var parent = $self.parent();
    var img = parent.find('.img-preview');
    var vid = parent.find('video');
    if (vid.length) {
        fname = vid.find('source').attr('src');
    } else {
        fname = img.attr('src');
    }
    var url = g_settings.base_url + 'social/create/upload_images?file=' + fname;
    if ($('[name="post_id"]').length) {
        url+='&post_id='+$('[name="post_id"]').val();
    }
    $.ajax({
      url: url,
      type: 'DELETE',
      success: function (success) {
        if (success) {
          $('.file').show();
          $file_name.empty();
          $form.find('input[name=image_name]').val('').trigger('change');
          parent.remove();
          var cou = $('.img-preview').length;
          if (cou == 0) {
            $form.find('input[name=attachment_type]').val('').trigger('change');
          }
            $(".inputFile").fileupload(options);
        }
      }
    });
    return false;
  });

});
})(jQuery);

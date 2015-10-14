(function ($) {

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


  $post_a_link_button.on('ifChecked', function () {
    load_blocks_html('get_post_a_link_html');
  });

  $suggested_links_button.on('ifChecked', function () {
    load_blocks_html('get_suggested_links_html');
  });

  $rss_feed_button.on('ifChecked', function () {
    load_blocks_html('get_custom_rss_feed_html');
  });

  /**
   * Hide schedule date settings (date / time / am \ pm)
   */
  $container.on('ifChanged', '#immediate-type', function () {
    $container.find('#schedule-settings').hide();
  });

  /**
   * Show schedule date settings (date / time / am \ pm)
   */
  $container.on('ifChanged', '#schedule-type', function () {
    $(".datepicker").datepicker({
      changeMonth: true,
      changeYear: true,
      showOtherMonths: true,
      dateFormat: "yy-mm-dd"
    });
    $container.find('#schedule-settings').show();
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
        }

        if ($container.find('div.suggested').length) {
          $('#rss-feed-container').html($loading);
          rss_paginate_load_html($container.find('div.suggested').data('url'), 1, '');
        }

      }
    })
  }

  /**
   * Send data to posting link
   */
  $container.on('submit', '#post-link-form', function () {
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

  /**
   * Custom RSS feed block - click on one of the user RSS titles
   * Load selected RSS feed into section right
   */
  $container.on('click', '.custom-rss-link', function () {
    var $self = $(this);
    $container.find('.custom-rss-link-parent').removeClass('active');
    $self.parent().addClass('active');
    $container.find('#rss-feed-container').html($loading);
    $.ajax({
      url: g_settings.base_url + 'social/create/get_rss_feed',
      type: 'POST',
      data: 'link=' + $self.data('url'),
      success: function (html) {
        $loading.replaceWith(html);
        $container.find('input').iCheck({
          checkboxClass: 'icheckbox_minimal-grey',
          radioClass: 'iradio_minimal-grey',
          increaseArea: '20%' // optional
        });
      }
    });
    return false;
  });

  /**
   * Previous pagination page
   * Get Page number, and decrement it
   * And checked RSS url
   */
  $container.on('click', '.previous-rss-page', function () {
    var $self = $(this);
    var pagenum = parseInt($self.siblings('.pgBody').find('span').html());
    if ($container.find('div.suggested').length) {
      var selected_rss_link = $container.find('div.suggested').data('url');
    } else {
      var selected_rss_link = $container.find('li.active').find('a').data('url');
    }
    var current_feed_html = $('#rss-feed-container').html();
    $('#rss-feed-container').html($loading);
    pagenum--;
    rss_paginate_load_html(selected_rss_link, pagenum, current_feed_html);
    return false;
  });

  /**
   * Next pagination page
   * Get Page number, and increment it
   * And checked RSS url
   */
  $container.on('click', '.next-rss-page', function () {
    var $self = $(this);
    var pagenum = parseInt($self.siblings('.pgBody').find('span').html());
    if ($container.find('div.suggested').length) {
      var selected_rss_link = $container.find('div.suggested').data('url');
    } else {
      var selected_rss_link = $container.find('li.active').find('a').data('url');
    }
    var current_feed_html = $('#rss-feed-container').html();
    $('#rss-feed-container').html($loading);
    pagenum++;
    rss_paginate_load_html(selected_rss_link, pagenum, current_feed_html);
    return false;
  });

  /**
   * Load RSS feed then user paginate results
   *
   * @param selected_rss_link
   * @param pagenum
   * @param current_feed_html
   */
  function rss_paginate_load_html(selected_rss_link, pagenum, current_feed_html) {
    $.ajax({
      url: g_settings.base_url + 'social/create/get_rss_feed',
      type: 'POST',
      data: 'link=' + selected_rss_link + '&pagenum=' + pagenum,
      success: function (html) {
        if (html.length > 1) {
          $loading.replaceWith(html);
        } else {
          $loading.replaceWith(current_feed_html);
        }
        $container.find('input').iCheck({
          checkboxClass: 'icheckbox_minimal-grey',
          radioClass: 'iradio_minimal-grey',
          increaseArea: '20%' // optional
        });
      }
    });
  }

  /**
   * Click of some of posts in RSS feed
   * (click on 'radio' to make it checked)
   */
  $container.on('ifChecked', 'input[name=custom_rss_feed_item]', function () {
    var $self = $(this);
    var $info_container = $self.parents('.post').find('.textBox');
    selected_link = $info_container.find('a').attr('href');
    selected_description = strip_tags($info_container.find('.rss-description').html());
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
   * Checked RSS post data sended to 'post a link' form
   */
  $container.on('click', '#post-custom-rss-link', function () {
    load_blocks_html('get_post_a_link_html');
    return false;
  });

})(jQuery);


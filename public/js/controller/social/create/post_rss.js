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
   * Contains 'post a link' form / Suggested RSS form / Custom RSS form
   *
   * @type {*|HTMLElement}
   */
  var $fcontainer = $('.feed-container');

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
            } else {
                alert('error');
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
        }

        if ($container.find('div.suggested').length) {
          $('#rss-feed-container').html($loading);
          rss_paginate_load_html($container.find('div.suggested').data('url'), 1, '');
        }

      }
    })
  }
  
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
    selected_description = strip_tags($info_container.find('.rss-description').html());
  });
  
  /**
   * Checked RSS post data sended to 'post a link' form
   */
  $container.on('click', '#post-custom-rss-link', function () {
    load_blocks_html('get_post_a_link_html');
    return false;
  });

})(jQuery);


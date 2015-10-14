(function($) {

    $('.dropdown-toggle').dropdown();

    $('.collapse').collapse();

    setTimeout(function() {
        $('.alert-disappearable').fadeOut(900);
    }, 3000);

    // $('.select_custom select').selectik({
    //     width: 225,
    //     maxItems: 5,
    //     customScroll:1,
    //     speedAnimation: 100
    // }); 

    $('.hiddenList').on('click', function(ev) {
        ev.stopPropagation();
    });

  $('.nav.nav-list .toggle').click(function(e){
    e.preventDefault();
    var $this = $(this);
    var isOpen = $this.hasClass('open');

    if (isOpen) {
      $this.children('.sub_menu').slideUp(function() {
        $this.removeClass('open');
      });
    } else {
      $this.children('.sub_menu').slideDown(function() {
        $this.addClass('open');
      });
    }

  });

    // activate toggle on page load
    $('.nav.nav-list .toggle').filter('.active,.open').find('.sub_menu').slideDown();


})(jQuery);
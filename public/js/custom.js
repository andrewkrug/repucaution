(function($) {
	$(document).ready(function() {
			// $(".contentBox").hide(); 
			$(".left_menu-block li:first").addClass("active").show();
			$(".contentBox:first").show(); 
			$(".left_menu-block li").click(function() {
				$(".left_menu-block li").removeClass("active"); 
				$(this).addClass("active"); 
				$(".contentBox").hide(); 
				var activeTab = $(this).children('a').attr("href"); 
				$(activeTab).fadeIn(); 
				return false;
			});
	// $('.hiddenList').on('click',function(){
	//  $(this).children('.sub_menu').toggle( "blind", 500);
	//  $(this).toggleClass('active');
	//  $(this).siblings('.hiddenList.active').children('.sub_menu').slideUp(500);
	//   $(this).siblings('.hiddenList.active').removeClass('active');
	// })

 $('.dropdown-toggle').dropdown();
 $(".collapse").collapse();
 //datepicker
 $(function() {
		$( ".datepicker" ).datepicker({
			changeMonth: true,
			changeYear: true,
			showOtherMonths:true,
		});
		});
//checkbox customer
 $('input').iCheck({
    checkboxClass: 'icheckbox_minimal-grey',
    radioClass: 'iradio_minimal-grey',
    increaseArea: '20%' // optional
  });
// $(".contentBlock-section").hide(); 
  var blockId=$('.iradio_minimal-grey.checked').parents('.controls').attr('id');
	var activeTab = $('#block_'+blockId);
	$(activeTab).fadeIn();
	$(window).load(function(){
	$(".iCheck-helper").on('click',function(){
      var blockId=$(this).parents('.controls').attr('id');
			var activeTab = $('#block_'+blockId);
			$(activeTab).fadeIn();
			$(activeTab).siblings('.contentBlock-section').hide();
			return false;
   });
	})
	// Customer select
	$(".select_block").each(function(indx){
		var self = $(this);
		  $(this).ddslick({
		  	width: self.data('width') || 174,
		  	height: self.data('height') || null
		  })
		});
	//include-exclude
	// $('.include-exclude').on('click', function(){
	// 	$(this).parents('.section-box').children('.include-exclude-box').toggle( "blind", 500);
	// 	$(this).parents('.section-box').siblings('.section-box').children('.include-exclude-box').slideUp(500);
	// 	$(this).toggleClass('closed');
	// })
	//Add, remove
	// $('#add').on('click',(function(){
	// 	var pastElem=$('.past').html();
	// 	$('.newBox').append(pastElem);
	// 	$('.newBox').show();
	// 	$('.newBox .remove').show();
	// 	$('.remove').click(function(){
	// 			$(this).parent().children().remove();
	// 			$('.newBox').hide();
	//   })
	// })
	// )
	// $('.removeBox').click(function(){
	// 	$(this).parent().remove();
	// })
	
	$(window).load(function(){
	$(".iradio_minimal-grey.checked").each(function(){
  	$(this).parent().next('.hiddenBox').slideDown();
		
		$(".iCheck-helper").on('click',function(){
        $(this).parents('.radioBox').next('.hiddenBox').slideDown();
		    $(this).parents('.controls').siblings().children('.radioBox').next('.hiddenBox').slideUp();
    });
  });
 });
 }); //end doc ready
	
})(jQuery);	


var hideBlock = function ($mainBlock, $hideBlock){
	$mainBlock.on('click', function(e) {
		if ($hideBlock.css('display') != 'block') {
			$mainBlock.toggleClass('active');
			$hideBlock.slideToggle('slow');
			var firstClick = true;
	        $(document).bind('click.myEvent', function(e) {
	            if (!firstClick && $(e.target).closest($hideBlock).length == 0) {
	                $hideBlock.slideUp('slow');
	                $mainBlock.removeClass('active');
	                $(document).unbind('click.myEvent');
	            }
	            firstClick = false;
	        });
		}
		e.preventDefault();
	})
};

var sidebarShow = function($link){
    //$link.closest('li').find('ul').removeClass('active');
    $link.on('click', function(){
        var $this = $(this);
        if($this.next('ul').length){
            var $item 		= $this.closest('li'),
                $parentItem = $item.parent(),
                $item_link  = $parentItem.find('a'),
                selfClick 	= $item.find('ul:first').is(':visible');
            if(!selfClick) {
                $parentItem.find('> li ul:visible').slideToggle();
                //$item.removeClass('active');
                $item_link.removeClass('active');
            }
            $item.find('ul:first').slideToggle();
            //$item.toggleClass('active');

            $this.toggleClass('active');
            //$item.find('ul:first').slideToggle();
            return false;
        }
    });
};

$.fn.equivHeight = function (){
	var $blocks = $(this),
		maxH    = $blocks.eq(0).height(); 			
	$blocks.each(function(){
		maxH = ( $(this).height() > maxH ) ? $(this).height() : maxH + 5 + '%';				
	});
	$blocks.height(maxH); 
};

var showSidebar = function(){
	var $sidebar 	= $('.sidebar');
	$('.btn-menu').on('click', function(){
		$sidebar.slideToggle('slow');
	})
};

var resizeFooter = function(){
	$('.main').removeAttr('style');
	$('.footer').removeAttr('style');
	if(($('.main_block').height() + 40) > $(window).height()){
		$('.main').height($(window).height() - 70 + 'px');
	} else {
			if($('.main').height() < $(window).height()){
			$('.main').height($(window).height() - 70 + 'px');
			$('.footer').css({
				'bottom':'0'
			})
		} else{
			$('.main').removeAttr('style');		
			$('.footer').css({
				'position':'relative'
			})
		}
	}
};

var radarContent = function(){
	$('.radar_content').height($('.box_dashboard').height() - $('.web_radar').find('.block_content_title').height() - $('.web_radar').find('.block_content_footer').height() - 43 + 'px'); 
};

var disabledInput = function(){
	var $disabled = $('.form-group').find('[disabled]');
	if($disabled){
		$disabled.closest('.form-group').addClass('disabled');
	}
};

var closeBlock = function($clickBlock, $blockCLose){
	$clickBlock.on('click', function(){
		$(this).closest($blockCLose).slideUp('slow');
	})
};
var showBlock = function($clickBlock, $dadBlock, $blockShow){
	$clickBlock.on('click', function(e){
		e.preventDefault();
		$(this).closest($dadBlock).find($blockShow).slideToggle();
	})
};

var removeMore = function($remove_more){
	$(document).on('click', $remove_more, function(e){
		e.preventDefault();
		$(this).closest('.row').remove();
		resizeFooter();
	})
};

var forBlockFooter = function($click_block){
	$(document).on('click', $click_block, function(){
		resizeFooter();
	})
};

var rating = function(){
	$('.progress-pie-chart').each(function(){
	    var $this 	= $(this),
	        rating 	= parseFloat($this.data('percent')),
	        percent = (rating/5)*100;
	        deg = 360*percent/100;
	    if (percent > 50) {
	        $this.addClass('gt-50');
	    }	   
	    $this.find('.ppc-progress-fill').css('transform','rotate('+ deg +'deg)');
	    $this.find('.ppc-percents span').html('<cite class="bold">'+ rating +'</cite>');
	});
};

var uploadFile = function($uploadBlock){
	$(this).each(function(){
		$('.fileSelect').on('click', function(e){
			e.preventDefault();
			$(this).closest($uploadBlock).find('.uploadbtn').click();
		})
	})		
};

var socialBorder = function($mainBlock){
	$mainBlock.each(function(){
		var $_this = $(this),
			$_icon = $_this.find('i');
		if($_icon.hasClass('i-twitter')){
			$_this.css('border-left', '2px solid #2fabe1');
			$_this.hover(function(){
				$(this).find('i').css('color', '#2fabe1');
			}, function() {
				$_icon.removeAttr('style');
			})
		} else if($_icon.hasClass('i-facebook')){
			$_this.css('border-left', '2px solid #3c5b9b');
			$_this.hover(function(){
				$_icon.css('color', '#3c5b9b');
			}, function() {
				$_icon.removeAttr('style');
			})
		} else if($_icon.hasClass('i-google')){
			$_this.css('border-left', '2px solid #f63e28');
			$_this.hover(function(){
				$_icon.css('color', '#f63e28');
			}, function() {
				$_icon.removeAttr('style');
			})
		} else if($_icon.hasClass('i-linkedin')){
			$_this.css('border-left', '2px solid #0079BA');
			$_this.hover(function(){
				$_icon.css('color', '#0079BA');
			}, function() {
				$_icon.removeAttr('style');
			})
		} else if($_icon.hasClass('i-instagram')){
			$_this.css('border-left', '2px solid #B17D4E');
			$_this.hover(function(){
				$_icon.css('color', '#B17D4E');
			}, function() {
				$_icon.removeAttr('style');
			})
		}		
	})
};

var masonryBlock = function(){
	var $container = $('.account_block');
	$container.imagesLoaded( function() {
		$container.masonry({
			itemSelector: '.account_item',
			columnWidth: '.account_item',
			transitionDuration: 0
		});
	});
};

var chooseAccountGroup = function($item, $dadBlock){
	$item.find('label').on('click', function(e){		
		e.preventDefault();
		var $this = $(this);
		$this.closest($dadBlock).find($item).removeClass('account_checked');
		$this.parent().addClass('account_checked');
	})
};

var showKeyword = function($clickBlock, $slideBlock){
	$clickBlock.on('click', function(e){
		e.preventDefault();
		$(this).closest('.form-group').find($slideBlock).slideToggle();
	})
};

var showKeywordProperties = function($mainblock, $clickBlockSelector, $slideBlock){
    $mainblock.on('click', $clickBlockSelector, function(e){
        e.preventDefault();
        $(this).closest('.form-group').find($slideBlock).slideToggle();
    })
};

CountGoods = {
	numericValidationString: /^[0-9]+$/
};

function ValidateNumeric(input){
	return CountGoods.numericValidationString.test(input);
}
var quantityInput = function($quantityBlock){
	var $incriments = $quantityBlock.find('.incrementBtn');
	var $decrements = $quantityBlock.find('.decrementBtn');
    $incriments.on('click', function () {  
    	var	$quantity = $(this).closest('.form-group').find('.quantity');
        if (ValidateNumeric($quantity.val())) {
            var number = parseInt($quantity.val());
            number--;
            if (number < 0) number = 0;
            $quantity.val(number);
        }
        else $quantity.val(0);
        $quantity.trigger('change');

        return false;
    });

 	$decrements.on('click', function () {
 		var $quantity = $(this).closest('.form-group').find('.quantity');
        if (ValidateNumeric($quantity.val())) {
            var number = parseInt($quantity.val());
            number++;
            if (number < 0) number = 0;
            $quantity.val(number);
        }
        else $quantity.val(0);
        $quantity.trigger('change');

        return false;
    });
};

var pieChart = function(){
    $('.progress-pie').each(function(){
        var $ppc = $(this),
            percent = parseFloat($ppc.data('percent')),
            deg = 360*percent/100;
        if (percent > 50) {
            $ppc.addClass('gt-50');
        }

        $ppc.find('.ppc-progress-fill').css('transform','rotate('+ deg +'deg)');
        $ppc.find('.ppc-percents span').html('<cite>'+ percent +'</cite>'+' %');
    })
};

var question = function(question, href) {
    var $question_modal = $('#question_modal');
    $('a', $question_modal).attr('href', href);
    $('.modal-body', $question_modal).html(
        '<h4 class="text-center">'+question+'</h4>'
    );
    $question_modal.modal('show');
    return false;
};

/**
 * Chart diagramm
 *
 * @param dataChart
 * @param chart_id
 * @param title
 */
var draw_chart = function(dataChart, chart_id, title) {

    Highcharts.setOptions({
        lang: {
            resetZoom: lang('reset_zoom'),
            months: lang('months'),
            shortMonths: lang('shortMonths')
        }
    });

    var dataLen = dataChart.length;
    if (dataLen) {
        $('#'+chart_id).highcharts({
            chart: {
                zoomType: 'x'
            },
            title: {
                text: title
            },
            subtitle: {
                text: document.ontouchstart === undefined ?
                    lang('click_and_drag_in_the_plot_area_to_zoom_in') :
                    lang('pinch_the_chart_to_zoom_in')
            },
            xAxis: {
                type: 'datetime'
            },
            yAxis: {
                title: {
                    text: title
                }
            },
            legend: {
                enabled: false
            },
            credits: {
                enabled: false
            },
            plotOptions: {
                area: {
                    fillColor: {
                        linearGradient: {
                            x1: 0,
                            y1: 0,
                            x2: 0,
                            y2: 1
                        },
                        stops: [
                            [0, Highcharts.getOptions().colors[0]],
                            [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                        ]
                    },
                    marker: {
                        radius: 2
                    },
                    lineWidth: 1,
                    states: {
                        hover: {
                            lineWidth: 1
                        }
                    },
                    threshold: null
                }
            },
            series: [{
                type: 'area',
                name: title,
                data: dataChart
            }]
        });

    } else {
        $('#'+chart_id).html('<p class="bold p-tl10">'+lang('no_data_available_yet')+'</p>');
    }
};

var lang = function(key, args) {
    if(!args || !args.length) {
        return g_settings.i18n[key];
    } else {
        var _value = g_settings.i18n[key];
        for(var i=0;i<args.length;i++) {
            _value = _value.replace('%s', args[i]);
        }
        return _value;
    }
};
<div class="demo">

<!-- ======[ Datepicker ]====== -->
<div class="rep-dp">
<div class="dp-val">
	<p>
	<i class="dp-icon-calendar"></i>
		<span id="span-start"><?php echo date('M d, Y', strtotime(Arr::get($dates, 'from', 'yesterday')));?></span>
		-
		<span id="span-end"><?php echo date('M d, Y', strtotime(Arr::get($dates, 'to', 'today')));?></span>
	</p>
</div>
<div class="dp-holder">    
		<div class="arrow-up">
			<small class="arrow-up inner"></small>
		</div>
		
        <div class="select-list">
		<select autocomplete="off">
		  <option value="">Select range</option>
		  <option selected="selected" value="<?php echo date('M d, Y', strtotime('yesterday')); ?>">Yesterday</option>
		  <option value="<?php echo date('M d, Y', strtotime('today - 7 weeks'));?>">Last 7 weeks</option>
		</select>
        </div>
		
        <div id="calendar_form">
            <input autocomplete="off" type="text" name="from" id="start" class="input-field" value="<?php echo date('M d, Y', strtotime(Arr::get($dates, 'from', 'yesterday')));?>"/>
				<span class="text-grey">&ndash;</span>
            <input autocomplete="off" type="text" name="to" id="end" class="input-field" value="<?php echo date('M d, Y', strtotime(Arr::get($dates, 'to', 'today')));?>"/>
        </div>
		
        <div id="calendars"></div>
        
		<div class="m-t10 text-right">
			<button type="button" id="apply-dates"  class="dp-btn primary">Apply</button>
			<button type="button" class="dp-btn plain sm text-ul exit-dp">Cancel</button> 
		</div>
</div>  
</div>  
<!-- ======[ Datepicker ]====== -->

</div>

<script src="<?php echo site_url('public/js/libs/calendar/prototype.js');?>"></script>
<script type="text/javascript"  src="<?php echo site_url('public/js/libs/calendar/timeframe.js');?>"></script>
<script>
  (function($) { 
		$(".dp-val").on('click', function(){
			$(".dp-holder").toggle();
		});	
		
		$('.exit-dp').on('click', function(){
			$(".dp-holder").hide();
		});
		
		$(document).mouseup(function (e){
			var dpHolder = $(".dp-holder");
		 
			if (!dpHolder.is(e.target) && dpHolder.has(e.target).length === 0){
				dpHolder.hide();
			}
		});


	})(jQuery); 	
  </script>
$(document).ready(function() {
  var cb = function(start, end, label) {
    $('.reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
  }

  var optionSet1 = {
    startDate: moment().subtract(29, 'days'),
    endDate: moment(),
    minDate: '01/01/2010',
    maxDate: moment(),
    showDropdowns: false,
    showWeekNumbers: false,
    timePicker: false,
    timePickerIncrement: 1,
    timePicker12Hour: true,
    ranges: {
      'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      'Last 7 Weeks': [moment().subtract(49, 'days'), moment()],
    },
    opens: 'left',
    format: 'MM/DD/YYYY',
    separator: ' to ',
    locale: {
      fromLabel: '',
      toLabel: '',
      customRangeLabel: 'Custom',
      daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr','Sa'],
      monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
      firstDay: 1
    },
    parentEl:'.date_range'
  };

  $('.reportrange span').html(moment().subtract(29, 'days').format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));   
 
  $('.reportrange').daterangepicker(optionSet1, cb);

   /*$('.reportrange').data('daterangepicker').setStartDate('04/01/2012');
   $('.reportrange').data('daterangepicker').setEndDate('08/02/2013');*/

  //$('.daterangepicker').detach().appendTo('.date_range');

  /*$(".ranges select").change(function() {
    switch ($(this).val()){
      case 'Yesterday': 
        $(".ranges ul li:first").trigger('click'); break;     
      case 'Last 7 weeks': 
        $(".ranges ul li:nth-child(2)").trigger('click'); break;
    }   
  });*/
});
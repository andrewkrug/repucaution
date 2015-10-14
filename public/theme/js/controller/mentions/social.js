$(function() {

	// highlight keywords

	var keywords = g_settings.keywords;

	$('#ajax-area .data p').each(function() {

        var text = $(this).text();


        for(var key in keywords) {

            var keyword = keywords[key]['keyword'],
                exact = keywords[key]['exact'] == '1',
                words = [];

            // if 'exact' option is not enabled use case-insensitive search
            var match_options = exact ? 'g' : 'gi';

            if (exact) {
                words = [keyword];
            } else {
                // split keyword into words by whitespace if 'exact' option is not enabled
                words = keyword.split(new RegExp('\\s+')); 
            }

            for(var w in words) {
                var matched = text.match(new RegExp(escape_regexp(words[w]), match_options));
                for(var m in matched) {
                    text = text.replace(matched[m], '<span class="developed">' + matched[m] + '</span>' );
                }
            }
		}

        $(this).html(text);
	});

    function escape_regexp(str) {
        return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
    }

    var $select = $('[name="keyword"]');
    $select.ddslick({
        width: 174,
        defaultSelectedIndex: 0,
        onSelected: function(ev) {

            g_settings.keyword_query_id || (g_settings.keyword_query_id = 0);

            if ((window.location.search.indexOf('keyword') === -1
                && window.location.search.indexOf('from') === -1
                && window.location.search.indexOf('to') === -1
                && window.location.search.indexOf('page') === -1
                && ev.selectedData.value != g_settings.keyword_query_id
                )
                || ev.selectedData.value != g_settings.keyword_query_id
            ) {
                window.location.href = window.location.pathname + '?keyword=' + ev.selectedData.value;
            }
        }
    });


	// initialize datepickers
    $(".datepicker").datepicker({
        changeMonth: true,
        changeYear: true,
        showOtherMonths:true,
        dateFormat: "M d, yy"
    }); 

    // get default dates
    var dates = g_settings.dates;

    // changing dates on date select
    $('.datepicker-from')
        .datepicker('option', 'onSelect', function(date) {
            dates.from = date;
        })
        .val(dates.from);

    // changing dates on date select
    $('.datepicker-to')
        .datepicker('option', 'onSelect', function(date) {
            dates.to = date;
        })
        .val(dates.to);


    // filter "Apply" buton click
    $('.filter').on('click', function() {     
    	var query_str = '?from=' + encodeURIComponent(dates.from) + '&to=' + encodeURIComponent(dates.to);
    	query_str += g_settings.keyword_query_str || '';
    	window.location.href = window.location.pathname + query_str;
    });

    

});